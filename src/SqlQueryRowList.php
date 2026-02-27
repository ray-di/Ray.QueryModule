<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Override;
use PDO;
use PDOStatement;
use Ray\Query\Exception\QueryNumException;

use function array_pop;
use function count;
use function explode;
use function preg_replace;
use function strpos;
use function strtolower;
use function trim;

/** @psalm-api */
class SqlQueryRowList implements RowListInterface
{
    public const QUERY_CLEANUP_REGEX = '/\/\*.*?\*\/|--.*$/m';
    public const TRIM_CHARACTERS_REGEX = "\\ \t\n\r\0\x0B";

    /** @var ExtendedPdoInterface */
    private $pdo;

    /** @var string */
    private $sql;

    public function __construct(ExtendedPdoInterface $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    /** @param array<string, mixed> ...$queries */
    #[Override]
    public function __invoke(array ...$queries): iterable
    {
        if (strpos($this->sql, ';') === false) {
            $this->sql .= ';';
        }

        $sqls = explode(';', trim($this->sql, self::TRIM_CHARACTERS_REGEX));
        array_pop($sqls);
        $numQueris = count($queries);
        if (count($sqls) !== $numQueris) {
            throw new QueryNumException($this->sql); // @codeCoverageIgnore
        }

        $result = null;
        for ($i = 0; $i < $numQueris; $i++) {
            $sql = $sqls[$i];
            $query = $queries[$i];
            $result = $this->pdo->perform($sql, $query);
        }

        $lastQuery = $result
            ? strtolower(trim(
                (string) preg_replace(self::QUERY_CLEANUP_REGEX, '', $result->queryString),
                self::TRIM_CHARACTERS_REGEX,
            )) : '';
        if ($result instanceof PDOStatement && strpos($lastQuery, 'select') === 0) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}
