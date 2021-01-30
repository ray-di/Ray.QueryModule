<?php

declare(strict_types=1);

namespace Ray\Query;

interface RowInterface extends QueryInterface
{
    /**
     * @param array ...$query
     *
     * @return array<string, scalar>
     */
    public function __invoke(array ...$query);
}
