<?php

declare(strict_types=1);

namespace Ray\Query;

class FakeTodoProviderSqlNotAnnotated
{
    public $todoCreate;

    public function __construct(
        InvokeInterface $todoCreate
    ){
        $this->todoCreate = $todoCreate;
    }
}
