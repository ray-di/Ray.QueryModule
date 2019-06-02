<?php
declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\AliasQuery;

class Todo
{
    /**
     * @AliasQuery("todo_item_by_id")
     */
    public function get(string $id)
    {
    }

    /**
     * @AliasQuery(id="todo_insert?id={uuid}", templated=true)
     */
    public function create(string $uuid, string $title)
    {
    }
}