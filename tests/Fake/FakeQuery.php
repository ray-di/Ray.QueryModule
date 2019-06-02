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
     * @Assisted({"todo"})
     * @Named("todo=todo_item_by_id")
     */
    public function get(string $uuid, QueryInterface $todo = null)
    {
        return $todo([
            'id' => $uuid
        ]);
    }

    /**
     * @Assisted({"createTodo"})
     * @Named("createTodo=todo_insert")
     */
    public function create(string $uuid, string $title, QueryInterface $createTodo = null)
    {
        return $createTodo([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
