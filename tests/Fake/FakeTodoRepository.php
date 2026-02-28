<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;

class FakeTodoRepository
{
    public function __construct(
        #[Sql('todo_insert')] public InvokeInterface $todoCreate,
        #[Sql('todo_item_by_id')] public RowInterface $todoItem,
        #[Sql('todo_list')] public RowListInterface $todoList
    ) {
    }
}
