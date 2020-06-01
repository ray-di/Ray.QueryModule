<?php

declare(strict_types=1);

namespace Ray\Query;

use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\Query\Fake\FakePhpQuery;

class PhpQueryModuleTest extends TestCase
{
    public function testHandBind() : void
    {
        $injector = new Injector(new class extends AbstractModule {
            protected function configure()
            {
                $this->bind()->annotatedWith('todo_item_by_id')->to(FakePhpQuery::class);
            }
        }, __DIR__ . '/tmp');
        $foo = $injector->getInstance(FakeFoo::class);
        $query = ['id' => '1'];
        $this->assertSame($query, $foo(['id' => '1']));
    }

    public function testModule() : void
    {
        $injector = new Injector(new class extends AbstractModule {
            protected function configure()
            {
                $queryBindings = [
                    'todo_item_by_id' => FakePhpQuery::class
                ];
                $this->install(new PhpQueryModule($queryBindings));
            }
        }, __DIR__ . '/tmp');
        $foo = $injector->getInstance(FakeFoo::class);
        $query = ['id' => '1'];
        $this->assertSame($query, $foo($query));
    }
}
