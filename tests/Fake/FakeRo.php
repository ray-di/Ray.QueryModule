<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use BEAR\Resource\ResourceObject;
use Ray\Query\Annotation\Query;

class FakeRo extends ResourceObject
{
    /**
     * @Query(id="todo_item_by_id", type="row")
     */
    #[Query('todo_item_by_id', type: 'row')]
    public function onGet(string $id)
    {
        return $this;
    }
}
