<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Query\Annotation\Query;

class ExampleQuery
{
    public function listNames(int $age) : void
    {
        $users = $this->getUsers($age);
        foreach ($users as $user) {
            echo $user['name'] . PHP_EOL;
        }
    }

    /**
     * @Query(id="users_by_age", type="row_list")
     *
     * @return array<array{id: string, name: string}>
     * @psalm-suppress InvalidReturnType
     */
    #[Query('users_by_age', type: 'row_list')]
    public function getUsers(int $age) : array
    {
        unset($age);
    }
}
