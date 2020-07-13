<?php

declare(strict_types=1);

namespace Ray\Query;

interface RowListInterface
{
    /**
     * @return list<array<string, scalar>>
     */
    public function __invoke(array ...$query);
}
