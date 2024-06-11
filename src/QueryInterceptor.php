<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use BEAR\Resource\ResourceObject;
use InvalidArgumentException;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Query\Annotation\Query;
use Ray\Query\Exception\SqlFileNotFoundException;

use function assert;
use function file_exists;
use function file_get_contents;
use function is_string;
use function parse_str;
use function parse_url;
use function sprintf;
use function strpos;
use function strstr;

class QueryInterceptor implements MethodInterceptor
{
    /** @var SqlDir */
    private $sqlDir;

    /** @var ExtendedPdoInterface */
    private $pdo;

    public function __construct(
        ExtendedPdoInterface $pdo,
        SqlDir $sqlDir
    ) {
        $this->sqlDir = $sqlDir;
        $this->pdo = $pdo;
    }

    /** @return ResourceObject|mixed */
    public function invoke(MethodInvocation $invocation)
    {
        $method = $invocation->getMethod();
        /** @var Query $query */
        $query = $method->getAnnotation(Query::class);

        $queryId = $query->id;
        if (strpos($queryId, '?') !== false) {
            $queryId = strstr($queryId, '?', true);
        }

        $file = sprintf('%s/%s.sql', $this->sqlDir->value, $queryId);
        if (! file_exists($file)) {
            throw new SqlFileNotFoundException($query->id, $query->id);
        }

        $sql = (string) file_get_contents($file);
        $query = $invocation->getMethod()->getAnnotation(Query::class);
        assert($query instanceof Query);
        /** @var array<string, mixed> $namedArguments */
        $namedArguments = (array) $invocation->getNamedArguments();
        [$queryId, $params] = $query->templated ? $this->templated($query, $namedArguments) : [$query->id, $namedArguments];
        $sqlQuery = $query->type === 'row' ? new SqlQueryRow($this->pdo, $sql) : new SqlQueryRowList($this->pdo, $sql);
        assert(is_string($queryId));
        assert($sqlQuery instanceof QueryInterface);

        /** @var array<string, mixed> $params */
        return $this->getQueryResult($invocation, $sqlQuery, $params);
    }

    /**
     * @param array<string, mixed> $param
     *
     * @return mixed
     */
    private function getQueryResult(MethodInvocation $invocation, QueryInterface $query, array $param)
    {
        /** @psalm-suppress MixedAssignment */
        $result = $query($param);
        $object = $invocation->getThis();
        if ($object instanceof ResourceObject) {
            return $this->returnRo($object, $invocation, $result);
        }

        return $result;
    }

    /** @param mixed $result */
    private function returnRo(ResourceObject $ro, MethodInvocation $invocation, $result): ResourceObject
    {
        if (! $result) {
            return $this->return404($ro);
        }

        $ro->body = $result;
        /** @var ResourceObject $ro */
        $ro = $invocation->proceed();

        return $ro;
    }

    private function return404(ResourceObject $ro): ResourceObject
    {
        $ro->code = 404;
        $ro->body = [];

        return $ro;
    }

    /**
     * @param array<string, mixed> $namedArguments
     *
     * @return array<int, mixed>
     */
    private function templated(Query $query, array $namedArguments): array
    {
        $url = parse_url(uri_template($query->id, $namedArguments));
        if (! isset($url['path'])) {
            throw new InvalidArgumentException($query->id);
        }

        $queryId = $url['path'];
        isset($url['query']) ? parse_str($url['query'], $params) : $params = $namedArguments;

        return [$queryId, $params + $namedArguments];
    }
}
