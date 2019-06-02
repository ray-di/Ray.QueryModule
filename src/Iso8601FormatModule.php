<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\AbstractModule;
use Ray\Query\Annotation\Iso8601;

class Iso8601FormatModule extends AbstractModule
{
    private $datetimeColumns;

    /**
     * @param array               $datetimeColumns
     * @param AbstractModule|null $module
     */
    public function __construct(array $datetimeColumns, AbstractModule $module = null)
    {
        $this->datetimeColumns = $datetimeColumns;
        parent::__construct($module);
    }

    protected function configure()
    {
        $this->bind('')->annotatedWith(Iso8601::class)->toInstance($this->datetimeColumns);
        $this->bind('')->annotatedWith('iso8601_date_time_columns')->toInstance($this->datetimeColumns);
        $this->bindInterceptor(
            $this->matcher->logicalOr(
                $this->matcher->subclassesOf(SqlQueryRow::class),
                $this->matcher->subclassesOf(SqlQueryRowList::class)
            ),
            $this->matcher->startsWith('__invoke'),
            [Iso8601Interceptor::class]
        );
    }
}