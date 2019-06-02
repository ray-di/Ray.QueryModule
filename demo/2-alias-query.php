<?php

declare(strict_types=1);

namespace Ray\Query;

/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

use Aura\Sql\ExtendedPdoInterface;
use Ray\AuraSqlModule\AuraSqlModule;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\Query\SqlQueryModule;

require dirname(__DIR__) . '/vendor/autoload.php';

require_once __DIR__ . '/Todo.php';

$injector = new Injector(new class extends AbstractModule
{
    protected function configure()
    {
        $this->install(new AuraSqlModule('sqlite::memory:'));
        $this->install(new SqlQueryModule(dirname(__DIR__ . '/sql')));
        $a = class_exists(Todo::class);
        $this->bind(Todo::class);
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
