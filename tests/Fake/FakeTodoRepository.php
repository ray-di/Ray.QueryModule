<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;

class FakeTodoRepository
{
    /**
     * @Sql("todo_insert")
     */
    public $todoCreate;

    /**
     * @Sql("todo_item_by_id")
     */
    public $todoItem;

    /**
     * @Sql("todo_list")
     */
    public $todoList;

    public function __construct(
        InvokeInterface  $todoCreate,
        RowInterface     $todoItem,
        RowListInterface $todoList
    ){
        $this->todoCreate = $todoCreate;
        $this->todoItem = $todoItem;
        $this->todoList = $todoList;
    }
}
