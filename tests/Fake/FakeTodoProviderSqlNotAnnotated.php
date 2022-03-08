<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;

class FakeTodoProviderSqlNotAnnotated
{
    public $todoCreate;

    public function __construct(
        InvokeInterface $todoCreate,
    ){
        $this->todoCreate = $todoCreate;
    }
}
