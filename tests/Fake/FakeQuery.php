<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\Di\Assisted;
use Ray\Di\Di\Named;

class FakeQuery
{
    /**
     * @var QueryInterface
     */
    private $todo;

    /**
     * @var QueryInterface
     */
    private $createTodo;

    /**
     * @Named("todo=todo_item_by_id, createTodo=todo_insert")
     */
    public function __construct(QueryInterface $todo, QueryInterface $createTodo)
    {
        $this->todo = $todo;
        $this->createTodo = $createTodo;
    }

    /**
     * @Assisted({"todo"})
     */
    public function get(string $uuid)
    {
        return ($this->todo)([
            'id' => $uuid
        ]);
    }

    /**
     * @Assisted({"createTodo"})
     */
    public function create(string $uuid, string $title)
    {
        return ($this->createTodo)([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
