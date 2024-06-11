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

class SqlQueryInterceptModuleTest extends TestCase
{
    /** @var FakeRo */
    private $fakeRo;

    /** @var FakeBar */
    private $fakeBar;

    protected function setUp(): void
    {
        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
        $pdo->query('CREATE TABLE IF NOT EXISTS todo (
          id INTEGER,
          title TEXT
)');
        $pdo->perform('INSERT INTO todo (id, title) VALUES (:id, :title) ', ['id' => 1, 'title' => 'run']);
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
                $this->install(new SqlQueryModule(__DIR__ . '/Fake/sql'));
                $this->bind(FakeBar::class);
            }
        };
        $injector = new Injector($module, __DIR__ . '/tmp');
        $this->fakeRo = $injector->getInstance(FakeRo::class);
        $this->fakeBar = $injector->getInstance(FakeBar::class);
    }

    public function testResourceObject200(): void
    {
        $response = $this->fakeRo->onGet('1');
        $this->assertSame(200, $response->code);
        $this->assertSame(['id' => '1', 'title' => 'run'], $response->body);
        $this->assertSame('{"id":"1","title":"run"}', (string) $response);
    }

    public function testResourceObject404(): void
    {
        $response = $this->fakeRo->onGet('2');
        $this->assertSame(404, $response->code);
        $this->assertSame([], $response->body);
        $this->assertSame('[]', (string) $response);
    }

    public function testNoSqlFile(): void
    {
        $this->expectException(SqlFileNotFoundException::class);
        $this->fakeRo->noSql();
    }

    public function testWithQuery(): void
    {
        $response = $this->fakeRo->withQuery('1');
        $this->assertSame(200, $response->code);
        $this->assertSame(['id' => '1', 'title' => 'run'], $response->body);
        $this->assertSame('{"id":"1","title":"run"}', (string) $response);
    }

    public function testNonResourceObject(): void
    {
        $response = $this->fakeBar->getIntercepted('1');
        $this->assertSame(['id' => '1', 'title' => 'run'], $response);
    }
}
