<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use function is_array;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Named;
use Ray\Query\Annotation\Iso8601;
use Ray\Query\Exception\QueryNumException;
use function count;
use function explode;
use function strpos;

final class Iso8601Interceptor implements MethodInterceptor
{
    /**
     * @var string[]
     */
    private $datetimeColumns;

    /**
     * @Named("datetimeColumns=iso8601_date_time_columns")
     * @param string[] $datetimeColumns
     */
    public function __construct(array $datetimeColumns)
    {
        $this->datetimeColumns = $datetimeColumns;
    }

    public function invoke(MethodInvocation $invocation)
    {
        $list = $invocation->proceed();
        if (! is_array($list)) {
            return $list;
        }
        if ($invocation->getThis() instanceof RowInterface) {
            return $list = $this->convert([$list])[0];
        }
        return $this->convert($list);
    }

    private function convert(array $list) : array
    {
        foreach ($list as &$row) {
            foreach ($row as $column => &$value) {
                if (in_array($column, $this->datetimeColumns)) {
                    $value = (new \DateTime($value))->format(\DateTime::ATOM);
                }
            }
        }

        return $list;
    }
}
