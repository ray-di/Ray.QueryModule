<?php

declare(strict_types=1);

namespace Ray\Query;

use DateTime;
use DateTimeImmutable;
use function is_array;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Named;
use Ray\Query\Annotation\Iso8601;

final class Iso8601Interceptor implements MethodInterceptor
{
    /**
     * @var string[]
     */
    private $datetimeColumns;

    /**
     * @Named("datetimeColumns=iso8601_date_time_columns")
     *
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

    /**
     * @param array<mixed> $list
     *
     * @return array<mixed>
     */
    private function convert(array $list) : array
    {
        foreach ($list as &$row) {
            foreach ($row as $column => &$value) {
                if (in_array($column, $this->datetimeColumns, true)) {
                    $value = (new DateTimeImmutable($value))->format(DateTime::ATOM);
                }
            }
        }

        return $list;
    }
}
