<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use PDO;
use PDOStatement;
use Ray\Query\Exception\QueryNumException;

use function array_pop;
use function count;
use function explode;
use function strpos;
use function strtolower;
use function trim;

class SqlQueryRowList implements RowListInterface
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
        if (! strpos($this->sql, ';')) {
            $this->sql .= ';';
        }

        $sqls = explode(';', trim($this->sql, "\\ \t\n\r\0\x0B"));
        array_pop($sqls);
        $numQueris = count($queries);
        if (count($sqls) !== $numQueris) {
            throw new QueryNumException($this->sql);
        }

        $result = null;
        for ($i = 0; $i < $numQueris; $i++) {
            $sql = $sqls[$i];
            $query = $queries[$i];
            $result = $this->pdo->perform($sql, $query);
        }

        $lastQuery = $result
            ? strtolower(trim($result->queryString, "\\ \t\n\r\0\x0B"))
            : '';
        if ($result instanceof PDOStatement && strpos($lastQuery, 'select') === 0) {
            return (array) $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}
