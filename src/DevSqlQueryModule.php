<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class DevSqlQueryModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->bind(GetSqlInterface::class)->to(DevGetSql::class)->in(Scope::SINGLETON);
    }
}
