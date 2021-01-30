<?php

declare(strict_types=1);

namespace Ray\Query;

use DateTime;
use DateTimeImmutable;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Named;

use function in_array;
use function is_array;

final class Iso8601Interceptor implements MethodInterceptor
{
    /** @var string[] */
    private $datetimeColumns;

    /**
     * @param string[] $datetimeColumns
     *
     * @Named("datetimeColumns=iso8601_date_time_columns")
     */
    #[Named('datetimeColumns=iso8601_date_time_columns')]
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
    private function convert(array $list): array
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
