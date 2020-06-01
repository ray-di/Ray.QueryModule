<?php

declare(strict_types=1);

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

    /**
     * @var AbstractModule
     */
    protected $module;

    protected function setUp() : void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title)', ['id' => '1', 'title' => 'run']);
        $this->module = new class($pdo) extends AbstractModule {
            /**
             * @var ExtendedPdo
             */
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

    public function testRowInterfaceInject() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodo::class);
        /* @var \Ray\Query\FakeQuery $todo */
        $actual = $todo->create('2', 'think');
        $this->assertSame([], $actual);
    }

    public function testCallableInject() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodo::class);
        /* @var \Ray\Query\FakeQuery $todo */
        $actural = $todo->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run'
        ];
        $this->assertSame($expected, $actural);
    }

    public function testAssistedQueryInterface() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeQuery::class);
        /* @var \Ray\Query\FakeQuery $todo */
        $actual = $todo->create('2', 'think');
        $this->assertSame([], $actual);
    }

    public function testAssistedQuery() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeQuery::class);
        /* @var \Ray\Query\FakeQuery $todo */
        $actual = $todo->get('1')[0]['title'];
        $this->assertSame('run', $actual);
    }

    public function testRowInterface() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $item = $injector->getInstance(FakeItem::class);
        /* @var \Ray\Query\FakeItem $item */
        $actual = $item(['id' => '1']);
        $this->assertSame(['id' => '1', 'title' => 'run'], $actual);
    }

    public function testRowListInterface() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $item = $injector->getInstance(FakeList::class);
        /* @var \Ray\Query\FakeItem $item */
        $actual = $item(['id' => '1']);
        $this->assertSame([['id' => '1', 'title' => 'run']], $actual);
    }

    public function testSqlAliasInterceptor() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        /* @var \Ray\Query\FakeAlias $fakeAlias */
        $fakeAlias = $injector->getInstance(FakeAlias::class);
        $actual = $fakeAlias->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run'
        ];
        $this->assertSame($expected, $actual);
    }

    public function testSqlAliasInterceptorWithNamed() : void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
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
