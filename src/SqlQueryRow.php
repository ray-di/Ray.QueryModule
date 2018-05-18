<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;

final class SqlQueryRow implements RowInterface
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

    public function __invoke(array $query) : iterable
    {
        $item = $this->pdo->fetchAssoc($this->sql, $query);

        return count($item) ? array_pop($item) : [];
    }
}
