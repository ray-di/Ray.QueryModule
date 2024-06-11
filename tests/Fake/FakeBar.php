<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\Di\Named;
use Ray\Query\Annotation\Query;
use Ray\Query\RowInterface;
use Ray\Query\RowListInterface;

class FakeBar
{
    /**
     * @Query(id="todo_item_by_id", type="row")
     */
    #[Query('todo_item_by_id', type: 'row')]
    public function getIntercepted(string $id)
    {
        return $this;
    }

}
