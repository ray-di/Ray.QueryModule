<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use PHPUnit\Framework\TestCase;

class SqlQueryTest extends TestCase
{
    private $pdo;

    public function setUp()
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
        $sql = file_get_contents(__DIR__ . '/Fake/sql/todo_item_by_id.sql');
        $query = new SqlQuery($this->pdo, $sql);
        $row = $query(['id' => 1])[0];
        $this->assertSame('run', $row['title']);
        $this->assertSame('1', $row['id']);
    }
}
