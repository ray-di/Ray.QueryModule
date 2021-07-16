<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\Di\Named;

class FakeTodo
{
    /**
     * @var callable
     */
    public $todoGet;

    /**
     * @var callable
     */
    private $todoCreate;

    /**
     * @var RowListInterface
     */
    private $todoList;

    /**
     * @Named("todoGet=todo_item_by_id, todoList=todo_list, todoCreate=todo_insert")
     */
    #[Named('todoGet=todo_item_by_id, todoList=todo_list, todoCreate=todo_insert')]
    public function __construct(RowInterface $todoGet, RowListInterface $todoList, callable $todoCreate)
    {
        $this->todoGet = $todoGet;
        $this->todoCreate = $todoCreate;
        $this->todoList = $todoList;
    }

    public function get(string $uuid)
    {
        $queries = [
            'id' => $uuid
        ];
        return ($this->todoGet)($queries);
    }

    public function getList(string $uuid)
    {
        return ($this->todoList)([
            'id' => $uuid
        ]);
    }

    public function create(string $uuid, string $title)
    {
        return ($this->todoCreate)([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
