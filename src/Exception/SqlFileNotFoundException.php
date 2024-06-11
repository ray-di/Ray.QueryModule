<?php

declare(strict_types=1);

namespace Ray\Query\Exception;

use LogicException;

class SqlFileNotFoundException extends LogicException
{
    /** @var string */
    public $sql;

    public function __construct(string $message, string $sql)
    {
        $this->sql = $sql;

        parent::__construct($message);
    }
}
