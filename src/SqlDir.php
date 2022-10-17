<?php

declare(strict_types=1);

namespace Ray\Query;

final class SqlDir
{
    /** @var string */
    public $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
