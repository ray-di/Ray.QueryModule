<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;

use function array_pop;
use function assert;
use function count;
use function is_iterable;

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
     * @param array<string, mixed> ...$queries
     */
    public function __invoke(array ...$queries): iterable
    {
        /** @var array<string, mixed> $query */
        $query = $queries[0];
        $item = $this->pdo->fetchAssoc($this->sql, $query);
        if (! count($item)) {
            return [];
        }

        $list = array_pop($item);
        assert(is_iterable($list));

        return $list;
    }
}
