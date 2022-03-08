<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;

class FakeTodoProviderSqlNotFound
{
    /**
     * @Sql("__invalid")
     */
    public $todoCreate;

    public function __construct(
        InvokeInterface $todoCreate,
    ){
        $this->todoCreate = $todoCreate;
    }
}
