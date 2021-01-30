<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;

use function array_pop;
use function count;

class SqlQueryRow implements RowInterface
{
    /** @var ExtendedPdoInterface */
    private $pdo;

    /** @var string */
    private $sql;

    public function __construct(ExtendedPdoInterface $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    /**
     * @param array<string, scalar> ...$queries
     *
     * @return iterable<mixed>
     */
    public function __invoke(array ...$queries): iterable
    {
        $query = $queries[0];
        $item = $this->pdo->fetchAssoc($this->sql, $query);

        return count($item) ? array_pop($item) : [];
    }
}
