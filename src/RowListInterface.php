<?php

declare(strict_types=1);

namespace Ray\Query;

interface RowListInterface extends QueryInterface
{
    /**
     * @return array
     */
    public function __invoke(array ...$query);
}
