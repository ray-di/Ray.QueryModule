<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
use Aura\Sql\ExtendedPdo;
use Ray\Query\QueryInterface;
use Ray\Query\SqlQuery;

require dirname(__DIR__) . '/vendor/autoload.php';

class Todo
{
    /**
     * @var QueryInterface
     */
    private $todoInsert;

    /**
     * @var QueryInterface
     */
    private $todoItem;

    public function __construct(
        QueryInterface $todoInsert,
        QueryInterface $todoItem
    ) {
        $this->todoInsert = $todoInsert;
        $this->todoItem = $todoItem;
    }

    public function get(string $uuid) : array
    {
        return ($this->todoItem)(['id' => $uuid]);
    }

    public function create(string $uuid, string $title)
    {
        ($this->todoInsert)([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}

$pdo = new ExtendedPdo('sqlite::memory:');
$pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
$todo = new Todo(
    new SqlQuery(
        $pdo,
        trim(file_get_contents(__DIR__ . '/sql/todo_insert.sql'))
    ),
    new SqlQuery(
        $pdo,
        trim(file_get_contents(__DIR__ . '/sql/todo_item.sql'))
    )
);
$todo->create('1', 'think');
$todo->create('2', 'walk');
var_dump($todo->get('1')[0]);
//array(4) {
//    'id' =>
//  string(1) "1"
//    [0] =>
//  string(1) "1"
//  'title' =>
//  string(5) "think"
//    [1] =>
//  string(5) "think"
//}
