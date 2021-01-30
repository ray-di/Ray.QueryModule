<?php

declare(strict_types=1);

namespace Ray\Query;

interface RowListInterface extends QueryInterface
{
    /**
     * @param array<string, scalar> ...$query
     *
     * @return array<iterable<string, scalar>>
     */
    public function __invoke(array ...$query): iterable;
}
