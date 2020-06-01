<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Di\Di\Assisted;
use Ray\Di\Di\Named;

class FakeQueryRow extends SqlQueryRow
{
    /**
     * @param array{name: string, age: int} $queries
     *
     * @return array<int, array{id: string}>
     *
     * @
     */
    public function __invoke(array ...$queries) : iterable
    {
        return parent::__invoke($queries);
    }
}

$list = (new FakeQueryRow)(['age'=>10, 'name'=>'a']);
foreach ($list as $item) {
    $item['id']
}
