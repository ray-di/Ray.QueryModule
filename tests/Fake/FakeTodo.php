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
     * @var RowInterface
     */
    private $todoGet2;

    /**
     * @Named("todoGet=todo_item_by_id, todoGet2=todo/item/by_id, todoCreate=todo_insert")
     */
    public function __construct(RowInterface $todoGet, RowInterface $todoGet2, callable $todoCreate)
    {
        $this->todoGet = $todoGet;
        $this->todoGet2 = $todoGet2;
        $this->todoCreate = $todoCreate;
    }

    public function get(string $uuid)
    {
        return ($this->todoGet)([
            'id' => $uuid
        ]);
    }

    public function get2(string $uuid)
    {
        return ($this->todoGet2)([
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
