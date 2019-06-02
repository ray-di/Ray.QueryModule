<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Ray\Query\Exception\QueryNumException;
use function count;
use function explode;
use function strpos;

class SqlQueryRowList implements RowListInterface
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

    public function __invoke(array ...$queries) : iterable
    {
        if (! strpos($this->sql, ';')) {
            $this->sql .= ';';
        }
        $sqls = explode(';', trim($this->sql, "\ \t\n\r\0\x0B"));
        array_pop($sqls);
        $numQueris = count($queries);
        if (count($sqls) !== $numQueris) {
            throw new QueryNumException($this->sql);
        }
        for ($i = 0; $i < $numQueris; $i++) {
            $sql = $sqls[$i];
            $query = $queries[$i];
            $result = $this->pdo->perform($sql, $query);
        }
        if (isset($result) && strpos(strtolower($result->queryString), 'select') === 0) {
            return (array) $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        return [];
    }
}
