<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;

class FakeTodoRepositoryAttr
{
    public function __construct(
        #[Sql('todo_insert')] public readonly InvokeInterface $todoCreate,
        #[Sql('todo_item_by_id')]  public readonly RowInterface $todoItem,
        #[Sql('todo_list')]  public readonly RowListInterface $todoList
    ){}
}
