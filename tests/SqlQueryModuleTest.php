<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use Aura\Sql\PdoInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

use function assert;
use function print_r;

class SqlQueryModuleTest extends TestCase
{
    /** @var ExtendedPdo */
    protected $pdo;

    /** @var AbstractModule */
    protected $module;

    protected function setUp(): void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title)', ['id' => '1', 'title' => 'run']);
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
            }
        };
    }

    public function testRowInterfaceInject(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodo::class);
        /** @var FakeQuery $todo */
        $actual = $todo->create('2', 'think');
        $this->assertSame([], $actual);
    }

    public function testCallableInject(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodo::class);
        /** @var FakeQuery $todo */
        $actural = $todo->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run',
        ];
        $this->assertSame($expected, $actural);
    }

    public function testAssistedQueryInterface(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeQuery::class);
        /** @var FakeQuery $todo */
        $actual = $todo->create('2', 'think');
        $this->assertSame([], $actual);
    }

    public function testAssistedQuery(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeQuery::class);
        /** @var FakeQuery $todo */
        $actual = $todo->get('1')[0]['title'];
        $this->assertSame('run', $actual);
    }

    public function testRowInterface(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $item = $injector->getInstance(FakeItem::class);
        /** @var FakeItem $item */
        $actual = $item(['id' => '1']);
        $this->assertSame(['id' => '1', 'title' => 'run'], $actual);
    }

    public function testRowListInterface(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $item = $injector->getInstance(FakeList::class);
        /** @var FakeItem $item */
        $actual = $item(['id' => '1']);
        $this->assertSame([['id' => '1', 'title' => 'run']], $actual);
    }

    public function testSqlAliasInterceptor(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        /** @var FakeAlias $fakeAlias */
        $fakeAlias = $injector->getInstance(FakeAlias::class);
        $actual = $fakeAlias->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run',
        ];
        $this->assertSame($expected, $actual);
    }

    public function testSqlAliasInterceptorWithNamed(): FakeAliasNamed
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        /** @var FakeAliasNamed $fakeAlias */
        $fakeAlias = $injector->getInstance(FakeAliasNamed::class);
        $actual = $fakeAlias->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run',
        ];
        $this->assertSame($expected, $actual);

        return $fakeAlias;
    }

    /**
     * @depends testSqlAliasInterceptorWithNamed
     */
    public function testTempalteError(FakeAliasNamed $ro): void
    {
        $this->expectException(InvalidArgumentException::class);
        $ro->templteError('1');
    }

    public function testResourceObject200(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        /** @var FakeRo $ro */
        $ro = $injector->getInstance(FakeRo::class);
        $response = $ro->onGet('1');
        $this->assertSame(200, $response->code);
        $this->assertSame(['id' => '1', 'title' => 'run'], $response->body);
        $this->assertSame('{"id":"1","title":"run"}', (string) $response);
    }

    public function testResourceObject404(): void
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        /** @var FakeRo $ro */
        $ro = $injector->getInstance(FakeRo::class);
        $response = $ro->onGet('2');
        $this->assertSame(404, $response->code);
        $this->assertSame([], $response->body);
        $this->assertSame('[]', (string) $response);
    }

    public function testDevSqlModule(): void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title)', ['id' => '1', 'title' => 'run']);
        $module = new class ($pdo) extends AbstractModule {
            /** @var ExtendedPdo */
            private $pdo;

            public function __construct(ExtendedPdo $pdo)
            {
                $this->pdo = $pdo;
            }

            protected function configure()
            {
                $this->bind(ExtendedPdoInterface::class)->toInstance($this->pdo);
                $this->install(new SqlQueryModule(__DIR__ . '/Fake/sql', null, new SqlFileName()));
            }
        };
        $injector = (new Injector($module));
        $todo = $injector->getInstance(FakeTodo::class);
        assert($todo instanceof FakeTodo);
        $pdo = $injector->getInstance(ExtendedPdoInterface::class);
        assert($pdo instanceof PdoInterface);
        $todo->create('1', 'a');
        $todo->get('1');
        $this->assertStringContainsString('/* todo_item_by_id.sql */ SELECT * FROM todo WHERE id = :id', print_r($todo, true));
    }
}
