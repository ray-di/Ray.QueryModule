<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\Di\Named;

class FakeFoo implements QueryInterface
{
    /**
     * @var callable
     */
    private $func;

    public function __construct(#[Named('todo_item_by_id')] callable $func)
    {
        $this->func = $func;
    }

    public function __invoke(array ...$queries) : iterable
    {
        $query = $queries[0];

        return ($this->func)($query);
    }
}
