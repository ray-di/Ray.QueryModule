<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use PHPUnit\Framework\TestCase;

class SqlQueryTest extends TestCase
{
    private $pdo;

    protected function setUp() : void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title)', ['id' => '1', 'title' => 'run']);
        $this->pdo = $pdo;
    }

    public function test__invoke()
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id.sql');
        $query = new SqlQueryRowList($this->pdo, $sql);
        $row = ((array) $query(['id' => 1]))[0];
        $this->assertSame('run', $row['title']);
        $this->assertSame('1', $row['id']);
    }

    public function testNotFound()
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id.sql');
        $query = new SqlQueryRowList($this->pdo, $sql);
        $row = $query(['id' => '__invalid__']);
        $this->assertSame([], $row);
    }

    public function testMultipleQuery()
    {
        $sql = (string) file_get_contents(__DIR__ . '/Fake/sql/multiple_query.sql');
        $query = new SqlQueryRowList($this->pdo, $sql);
        $row = ((array) $query(
            [
                'id' => 2,
                'title' => 'test'
            ],
            [
                'id' => 2
            ]
        ))[0];
        $this->assertSame('test', $row['title']);
        $this->assertSame('2', $row['id']);
    }
}
