<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @Target("PROPERTY")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Sql
{
    /** @var string */
    public $sql;

    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }
}
