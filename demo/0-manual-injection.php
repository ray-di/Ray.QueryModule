<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

use Aura\Sql\ExtendedPdo;
use Ray\Query\SqlQueryRowList;

require dirname(__DIR__) . '/vendor/autoload.php';

class Todo
{
    /**
     * @var callable
     */
    private $createTodo;

    /**
     * @var callable
     */
    private $todoItem;

    public function __construct(
        callable $createTodo,
        callable $todoItem
    ) {
        $this->createTodo = $createTodo;
        $this->todoItem = $todoItem;
    }

    public function get(string $uuid) : array
    {
        return ($this->todoItem)(['id' => $uuid]);
    }

    public function create(string $uuid, string $title)
    {
        ($this->createTodo)([
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
    new SqlQueryRowList(
        $pdo,
        trim(file_get_contents(__DIR__ . '/sql/todo_insert.sql'))
    ),
    new SqlQueryRowList(
        $pdo,
        trim(file_get_contents(__DIR__ . '/sql/todo_item_by_id.sql'))
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
