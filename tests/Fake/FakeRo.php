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

    #[Query('_non_exists_')]
    public function noSql(): void
    {
    }

    /**
     * @Query(id="todo_item_by_id?id=num", type="row", templated=true)
     */
    #[Query('todo_item_by_id?id={num}', type: 'row', templated: true)]
    public function withQuery(string $num): ResourceObject
    {
        return $this;
    }

    #[Query('todo_item_by_id', type: 'row')]
    public function wrongPath()
    {
    }
}
