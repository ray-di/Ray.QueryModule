<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Query\Annotation\AliasQuery;

class FakeAliasNamed
{
    /**
     * @AliasQuery(id="todo_item_by_id?id={a}", templated=true, type="item")
     */
    public function get(string $a)
    {
    }
}
