<?php

declare(strict_types=1);

namespace Ray\Query;

use BEAR\Resource\ResourceObject;
use InvalidArgumentException;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Aop\ReflectionMethod;
use Ray\Di\InjectorInterface;
use Ray\Query\Annotation\Query;

use function is_string;
use function parse_str;
use function parse_url;

class QueryInterceptor implements MethodInterceptor
{
    /** @var InjectorInterface */
    private $injector;

    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
    }

    /**
     * @return ResourceObject|mixed
     */
    public function invoke(MethodInvocation $invocation)
    {
        /** @var ReflectionMethod $method */
        $method = $invocation->getMethod();
        /** @var Query $query */
        $query = $method->getAnnotation(Query::class);
        /** @var array<string, mixed> $namedArguments */
        $namedArguments = (array) $invocation->getNamedArguments();
        [$queryId, $params] = $query->templated ? $this->templated($query, $namedArguments) : [$query->id, $namedArguments];
        $interface = $query->type === 'row' ? RowInterface::class : RowListInterface::class;
        assert(is_string($queryId));
        /** @var RowInterface|RowListInterface|object  $query */
        $query = $this->injector->getInstance($interface, $queryId);
        if ($query instanceof QueryInterface) {
            /** @var array<string, mixed> $params */
            return $this->getQueryResult($invocation, $query, $params);
        }

        return $invocation->proceed();
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

    /**
     * @param mixed $result
     */
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
        if (! isset($url['path'])) { // @phpstan-ignore-line
            throw new InvalidArgumentException($query->id);
        }

        $queryId = $url['path'];
        isset($url['query']) ? parse_str($url['query'], $params) : $params = $namedArguments;

        $a = [$queryId, $params + $namedArguments];

        return $a;
    }
}
