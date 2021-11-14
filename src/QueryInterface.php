<?php

declare(strict_types=1);

namespace Ray\Query;

interface QueryInterface
{
    /**
     * @param array<string, mixed> ...$query
     *
     * @return mixed
     */
    public function __invoke(array ...$query);
}
