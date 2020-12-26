<?php

declare(strict_types=1);

namespace Ray\Query;

interface QueryInterface
{
    /**
     * @return array
     */
    public function __invoke(array ...$query);
}
