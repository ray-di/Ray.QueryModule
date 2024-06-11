<?php

declare(strict_types=1);

use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Di\Named;
use Ray\Di\Injector;
use Ray\Query\Annotation\Sql;
use Ray\Query\QueryInterface;
use Ray\Query\CallableQueryModule;
use Ray\Query\SqlQueryModule;

require dirname(__DIR__) . '/vendor/autoload.php';

class Todo
{
    public function __construct(
        #[Sql('todo_insert')] private QueryInterface $createTodo,
        #[Sql('todo_item_by_id')] private QueryInterface $todoItem,
    ) {}

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

$injector = new Injector(new class extends AbstractModule {
    protected function configure()
    {
        $this->install(new Ray\AuraSqlModule\AuraSqlModule('sqlite::memory:'));
        $this->install(new CallableQueryModule(dirname(__DIR__ . '/sql')));
        $this->install(new SqlQueryModule(dirname(__DIR__ . '/sql')));
    }
});
/** @var Todo $todo */
$pdo = $injector->getInstance(ExtendedPdoInterface::class);
$pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
$todo = $injector->getInstance(Todo::class);
$todo->create('1', 'think');
$todo->create('2', 'walk');
var_dump($todo->get('1'));

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
