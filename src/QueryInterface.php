<?php

declare(strict_types=1);

namespace Ray\Query;

interface QueryInterface
{
    public function __invoke(array ...$query) : iterable;
}
