<?php

declare(strict_types=1);

namespace Ray\Query;

interface RowInterface extends QueryInterface
{
    /**
     * @param array<string, scalar> ...$query
     *
     * @return iterable<string, scalar>
     */
    public function __invoke(array ...$query): iterable;
}
