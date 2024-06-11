<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Di\AbstractModule;
use Ray\Query\Annotation\Query;

class SqlQueryInterceptModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Query::class),
            [QueryInterceptor::class],
        );
    }
}
