<?php

declare(strict_types=1);

namespace Ray\Query;

use Override;
use Ray\Di\AbstractModule;

/** @psalm-api */
final class Iso8601FormatModule extends AbstractModule
{
    /** @var array<string> */
    private $datetimeColumns;

    /** @param array<string> $datetimeColumns */
    public function __construct(array $datetimeColumns, ?AbstractModule $module = null)
    {
        $this->datetimeColumns = $datetimeColumns;

        parent::__construct($module);
    }

    #[Override]
    protected function configure()
    {
        $this->bind()->annotatedWith('iso8601_date_time_columns')->toInstance($this->datetimeColumns);
        $this->bindInterceptor(
            $this->matcher->logicalOr(
                $this->matcher->subclassesOf(SqlQueryRow::class),
                $this->matcher->subclassesOf(SqlQueryRowList::class),
            ),
            $this->matcher->startsWith('__invoke'),
            [Iso8601Interceptor::class],
        );
    }
}
