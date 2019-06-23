<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class Iso8601FormatModuleTest extends TestCase
{
    /**
     * @var ExtendedPdo
     */
    protected $pdo;

    protected $module;

    protected function setUp() : void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT,
          created_at TIMESTAMP)');
        $pdo->perform('INSERT INTO todo (id, title, created_at) VALUES (:id, :title, :created_at)', ['id' => '1', 'title' => 'run', 'created_at' => '1970-01-01 00:00:00']);
        $this->module = new class($pdo) extends AbstractModule {
            private $pdo;

            public function __construct(ExtendedPdo $pdo)
            {
                $this->pdo = $pdo;
                parent::__construct();
            }

            protected function configure()
            {
                $this->bind(ExtendedPdoInterface::class)->toInstance($this->pdo);
                $this->install(new SqlQueryModule(__DIR__ . '/Fake/sql'));
                $this->install(new Iso8601FormatModule(['created_at']));
            }
        };
    }

    public function testItem()
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodo::class);
        /* @var \Ray\Query\FakeTodo $todo */
        $actural = $todo->get('1');
        $expected = [
            'id' => '1',
            'title' => 'run',
            'created_at' => '1970-01-01T00:00:00+00:00'
        ];
        $this->assertSame($expected, $actural);
    }

    public function testList()
    {
        $injector = new Injector($this->module, __DIR__ . '/tmp');
        $todo = $injector->getInstance(FakeTodo::class);
        /* @var \Ray\Query\FakeTodo $todo */
        $actural = $todo->getList('1');
        $expected = [
            [
                'id' => '1',
                'title' => 'run',
                'created_at' => '1970-01-01T00:00:00+00:00'
            ]
        ];
        $this->assertSame($expected, $actural);
    }
}
