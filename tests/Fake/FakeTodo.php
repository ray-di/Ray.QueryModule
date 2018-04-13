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

class FakeTodo
{
    /**
     * @Assisted({"todoSelect"})
     * @Named("todoSelect=todo_item")
     */
    public function get(string $uuid, QueryInterface $todoSelect = null)
    {
        return $todoSelect([
            'id' => $uuid
        ]);
    }

    /**
     * @Assisted({"todoInsert"})
     * @Named("todoInsert=todo_insert")
     */
    public function create(string $uuid, string $title, QueryInterface $todoInsert = null)
    {
        return $todoInsert([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
