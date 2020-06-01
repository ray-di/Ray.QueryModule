<?php

declare(strict_types=1);

namespace Ray\Query;

use BEAR\Resource\ResourceObject;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Aop\ReflectionMethod;
use Ray\Di\InjectorInterface;
use Ray\Query\Annotation\Query;

class QueryInterceptor implements MethodInterceptor
{
    /**
     * @var InjectorInterface
     */
    private $injector;

    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
    }

    public function invoke(MethodInvocation $invocation)
    {
        /** @var ReflectionMethod $method */
        $method = $invocation->getMethod();
        /** @var Query $query */
        $query = $method->getAnnotation(Query::class);
        $namedArguments = (array) $invocation->getNamedArguments();
        [$queryId, $params] = $query->templated ? $this->templated($query, $namedArguments) : [$query->id, $namedArguments];
        $interface = $query->type === 'row' ? RowInterface::class : RowListInterface::class;
        $query = $this->injector->getInstance($interface, $queryId);
        if ($query instanceof QueryInterface) {
            return $this->getQueryResult($invocation, $query, $params);
        }

        return $invocation->proceed();
    }

    /**
     * @param array<int,mixed> $param
     *
     * @return mixed
     */
    private function getQueryResult(MethodInvocation $invocation, QueryInterface $query, array $param)
    {
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
    private function returnRo(ResourceObject $ro, MethodInvocation $invocation, $result) : ResourceObject
    {
        if (! $result) {
            return $this->return404($ro);
        }
        $ro->body = $result;

        return $invocation->proceed();
    }

    private function return404(ResourceObject $ro) : ResourceObject
    {
        $ro->code = 404;
        $ro->body = [];

        return $ro;
    }

    /**
     * @param array<string, mixed> $namedArguments
     *
     * @return array{0: string, 1: array}
     */
    private function templated(Query $query, array $namedArguments) : array
    {
        $url = parse_url(uri_template($query->id, $namedArguments));
        if (! isset($url['path'])) { // @phpstan-ignore-line
            throw new \InvalidArgumentException($query->id);
        }
        $queryId = $url['path'];
        isset($url['query']) ? parse_str($url['query'], $params) : $params = $namedArguments;

        return [$queryId, $params + $namedArguments];
    }
}
