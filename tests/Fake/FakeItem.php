<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\Di\Named;

class FakeItem implements QueryInterface
{
    /**
     * @var RowInterface
     */
    private $func;

    public function __construct(#[Named('todo_item_by_id')] RowInterface $func)
    {
        $this->func = $func;
    }

    public function __invoke(array ...$queries)
    {
        $query = $queries[0];

        return ($this->func)($query);
    }
}
