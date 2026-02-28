<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;

class FakeTodoProviderSqlNotFound
{
    public function __construct(
        #[Sql('__invalid')] public InvokeInterface $todoCreate
    ) {
    }
}
