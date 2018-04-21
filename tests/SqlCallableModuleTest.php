<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class SqlCallableModuleTest extends TestCase
{
    /**
     * @var ExtendedPdo
     */
    protected $pdo;

    protected $module;

    protected function setUp()
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title)', ['id' => '1', 'title' => 'run']);
        $this->module = new class($pdo) extends AbstractModule {
            private $pdo;

            public function __construct(ExtendedPdo $pdo)
            {
                $this->pdo = $pdo;
            }

            protected function configure()
            {
                $this->bind(ExtendedPdoInterface::class)->toInstance($this->pdo);
                $this->install(new SqlQueryModule(__DIR__ . '/Fake/sql'));
            }
        };
    }

    public function testCreate()
    {
        $injector = new Injector($this->module);
        $todo = $injector->getInstance(FakeTodos::class);
        /* @var \Ray\Query\FakeTodo $todo */
        $actual = $todo->create('2', 'think');
        $this->assertSame([], $actual);
    }

    public function testGet()
    {
        $injector = new Injector($this->module);
        $todo = $injector->getInstance(FakeTodo::class);
        /* @var \Ray\Query\FakeTodo $todo */
        $actual = $todo->get('1')[0]['title'];
        $this->assertSame('run', $actual);
    }
}
