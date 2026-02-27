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
    public function get(string $uuid, #[Assisted, Named('todo_item_by_id')] ?QueryInterface $todo = null)
    {
        assert(is_callable($todo));
        return $todo([
            'id' => $uuid
        ]);
    }

    public function create(string $uuid, string $title, #[Assisted, Named('todo_insert')] ?QueryInterface $createTodo = null)
    {
        assert(is_callable($createTodo));
        return $createTodo([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
