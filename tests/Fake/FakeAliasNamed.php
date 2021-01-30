<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Query\Annotation\Query;

class FakeAliasNamed
{
    /**
     * @Query(id="todo_item_by_id?id={a}", templated=true, type="row")
     */
    #[Query(id: 'todo_item_by_id?id={a}', templated: true, type: 'row')]
    public function get(string $a)
    {
    }

    /**
     * @Query(id="", templated=true)
     */
    #[Query(id: '?id={b}', templated: true)]
    public function templteError(string $a)
    {
    }
}
