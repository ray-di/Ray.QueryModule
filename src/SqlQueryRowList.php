<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;

final class SqlQueryRowList implements RowListInterface
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
        $result = $this->pdo->perform($this->sql, $query);
        if (strpos(strtolower($result->queryString), 'select') === 0) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        return [];
    }
}
