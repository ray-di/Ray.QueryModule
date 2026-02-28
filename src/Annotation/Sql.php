<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Sql
{
    /** @var string */
    public $sql;

    /** @psalm-api */
    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }
}
