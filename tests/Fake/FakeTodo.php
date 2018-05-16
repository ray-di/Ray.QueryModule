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
    private $todoGet;

    /**
     * @var callable
     */
    private $todoCreate;

    /**
     * @Named("todoGet=todo_item_by_id, todoCreate=todo_insert")
     */
    public function __construct(RowInterface $todoGet, callable $todoCreate)
    {
        $this->todoGet = $todoGet;
        $this->todoCreate = $todoCreate;
    }

    public function get(string $uuid)
    {
        return ($this->todoGet)([
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
