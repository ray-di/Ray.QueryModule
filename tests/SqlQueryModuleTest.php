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

class SqlQueryModuleTest extends TestCase
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
        $todo = $injector->getInstance(FakeTodo::class);
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

    public function testItemInterface()
    {
        $injector = new Injector($this->module);
        $item = $injector->getInstance(FakeItem::class);
        /* @var \Ray\Query\FakeItem $item */
        $actual = $item(['id' => '1']);
        $this->assertSame(['id' => '1', 'title' => 'run'], $actual);
    }

    public function testListInterface()
    {
        $injector = new Injector($this->module);
        $item = $injector->getInstance(FakeList::class);
        /* @var \Ray\Query\FakeItem $item */
        $actual = $item(['id' => '1']);
        $this->assertSame([['id' => '1', 'title' => 'run']], $actual);
    }

    public function testSqlAliasInterceptor()
    {
        $injector = new Injector($this->module);
        /* @var \Ray\Query\FakeAlias $fakeAlias */
        $fakeAlias = $injector->getInstance(FakeAlias::class);
        $actual = $fakeAlias->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run'
        ];
        $this->assertSame($expected, $actual);
    }

    public function testSqlAliasInterceptorWithNamed()
    {
        $injector = new Injector($this->module);
        /* @var \Ray\Query\FakeAlias $fakeAlias */
        $fakeAlias = $injector->getInstance(FakeAliasNamed::class);
        $actual = $fakeAlias->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run'
        ];
        $this->assertSame($expected, $actual);
    }
}
