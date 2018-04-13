<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Di\Assisted;
use Ray\Di\Di\Named;
use Ray\Di\Injector;
use Ray\Query\QueryInterface;
use Ray\Query\SqlQueryModule;

require dirname(__DIR__) . '/vendor/autoload.php';

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new Ray\AuraSqlModule\AuraSqlModule('sqlite::memory:'));
        $this->install(new SqlQueryModule(dirname(__DIR__ . '/sql')));
    }
}

class Todo
{
    /**
     * @Assisted({"todoItem"})
     * @Named("todoItem=todo_item")
     */
    public function get(string $uuid, QueryInterface $todoItem = null)
    {
        return $todoItem([
            'id' => $uuid
        ]);
    }

    /**
     * @Assisted({"todoInsert"})
     * @Named("todoInsert=todo_insert")
     */
    public function create(string $uuid, string $title, QueryInterface $todoInsert = null)
    {
        $todoInsert([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}

$injector = new Injector(new AppModule);
/** @var Todo $todo */
$pdo = $injector->getInstance(ExtendedPdoInterface::class);
$pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
$todo = $injector->getInstance(Todo::class);
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
