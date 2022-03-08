<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use PDO;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\Query\Exception\SqlFileNotFoundException;
use Ray\Query\Exception\SqlNotAnnotatedException;

use function count;

class SqlQueryProviderModuleTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $this->module = new class ($pdo) extends AbstractModule {
            /** @var ExtendedPdo */
            private $pdo;

            public function __construct(ExtendedPdo $pdo)
            {
                $this->pdo = $pdo;
            }

            protected function configure()
            {
                $this->bind(ExtendedPdoInterface::class)->toInstance($this->pdo);
                $this->install(new SqlQueryModule(__DIR__ . '/Fake/sql'));
                $this->install(new SqlQueryProviderModule());
            }
        };
    }

    public function testProviderInject(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodoRepository::class);
        $this->todoTest($todo);
    }

    /**
     * @requires PHP 8.1
     */
    public function testProviderInjectAttr(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodoRepositoryAttr::class);
        $this->todoTest($todo);
    }

    public function todoTest($todo)
    {
        /** @var FakeTodoRepository $todo */
        ($todo->todoCreate)(['id' => 1, 'title' => 'think']);
        ($todo->todoCreate)(['id' => 2, 'title' => 'travel']);
        $list = ($todo->todoList)([]);
        $this->assertSame(2, count($list));
        $item = ($todo->todoItem)(['id' => 2]);
        $this->assertSame('travel', $item['title']);
    }

    public function testSqlFileNotFoundException()
    {
        $this->expectException(SqlFileNotFoundException::class);
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $injector->getInstance(FakeTodoProviderSqlNotFound::class);
    }

    public function testSqlNotAnnotated()
    {
        $this->expectException(SqlNotAnnotatedException::class);
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $injector->getInstance(FakeTodoProviderSqlNotAnnotated::class);
    }
}
