<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query\Fake;

use Ray\Query\QueryInterface;

class FakePhpQuery implements QueryInterface
{
    public function __invoke(array ...$queries)
    {
        $query = $queries[0];

        return $query;
    }
}
