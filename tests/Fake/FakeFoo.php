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
     * @var QueryInterface
     */
    private $query;

    /**
     * @Named("todos_item")
     */
    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function __invoke(array $query)
    {
        return $this->query->__invoke($query);
    }
}
