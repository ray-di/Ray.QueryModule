<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

use Attribute;

/**
 * Injects a query object that runs the SQL file named by `sql`
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Sql
{
    /**
     * SQL file name in the SQL directory, without `.sql`
     *
     * @var string
     */
    public $sql;

    /** @psalm-api */
    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }
}
