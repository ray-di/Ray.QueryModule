<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

interface RowInterface extends QueryInterface
{
    public function __invoke(array $query) : iterable;
}
