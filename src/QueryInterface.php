<?php

declare(strict_types=1);

namespace Ray\Query;

interface QueryInterface
{
    /**
     * @param array<string, scalar> $query
     *
     * @return iterable<mixed>
     */
    public function __invoke(array ...$query);
}
