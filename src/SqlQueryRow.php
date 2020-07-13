<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;

class SqlQueryRow implements RowInterface
{
    /**
     * @var ExtendedPdoInterface
     */
    private $pdo;

    /**
     * @var string
     */
    private $sql;

    public function __construct(ExtendedPdoInterface $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    public function __invoke(array ...$queries) : array
    {
        $query = $queries[0];
        $item = $this->pdo->fetchAssoc($this->sql, $query);

        return count($item) ? array_pop($item) : [];
    }
}
