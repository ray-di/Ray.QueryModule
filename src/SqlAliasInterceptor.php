<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Aop\ReflectionMethod;
use Ray\Di\InjectorInterface;
use Ray\Query\Annotation\AliasQuery;

class SqlAliasInterceptor implements MethodInterceptor
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
        /** @var AliasQuery $aliasQuery */
        $aliasQuery = $method->getAnnotation(AliasQuery::class);
        /** @var QueryInterface $query */
        $query = $this->injector->getInstance('', $aliasQuery->id);
        $args = $invocation->getArguments();
        $paramas = $invocation->getMethod()->getParameters();
        $namedParams = [];
        foreach ($paramas as $param) {
            $namedParams[$param->getName()] = $args[$param->getPosition()];
        }
        $queryResult = $query($namedParams);

        return $queryResult;
    }
}
