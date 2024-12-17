<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use PDO;
use PHPUnit\Framework\TestCase;

use function assert;
use function file_get_contents;
use function is_array;

class SqlQueryTest extends TestCase
{
    /** @var ExtendedPdo */
    private $pdo;

    protected function setUp(): void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title)', ['id' => '1', 'title' => 'run']);
        $this->pdo = $pdo;
    }

    public function testInvoke(): void
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id.sql');
        $this->testSql($sql);
    }

    public function testNotFound(): void
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id.sql');
        $query = new SqlQueryRowList($this->pdo, $sql);
        $row = $query(['id' => '__invalid__']);
        $this->assertSame([], $row);
    }

    public function testMultipleQuery(): void
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/multiple_query.sql');
        $query = new SqlQueryRowList($this->pdo, $sql);
        $row = ((array) $query(
            [
                'id' => 2,
                'title' => 'test',
            ],
            ['id' => 2],
        ))[0];
        assert(is_array($row));
        assert(isset($row['title']));
        assert(isset($row['id']));
        $this->assertSame('test', $row['title']);
        $this->assertSame('2', $row['id']);
    }

    public function testWithComment(): void
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id_with_comment.sql');
        $this->testSql($sql);
    }

    public function testWithLineComment(): void
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id_with_line_comment.sql');
        $this->testSql($sql);
    }

    public function testWithMultipleComment(): void
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id_with_multiple_comment.sql');
        $this->testSql($sql);
    }

    private function testSql(string $sql): void
    {
        $query = new SqlQueryRowList($this->pdo, $sql);
        $row = ((array) $query(['id' => 1]))[0];
        assert(is_array($row));
        assert(isset($row['title']));
        assert(isset($row['id']));
        $this->assertSame('run', $row['title']);
        $this->assertSame('1', $row['id']);
    }
}
