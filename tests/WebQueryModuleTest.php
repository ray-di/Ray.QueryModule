<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class WebQueryModuleTest extends TestCase
{
    /**
     * @var AbstractModule
     */
    private $module;

    public function setUp()
    {
        $webQueryConfig = [
            'foo' => ['GET', 'https://httpbin.org/anything/foo'],
            'bar' => ['GET', 'https://httpbin.org/anything/bar']
        ];
        $guzzleConfig = [];
        $this->module = new WebQueryModule($webQueryConfig, $guzzleConfig);
    }

    public function testQueryInterface()
    {
        $foo = (new Injector($this->module))->getInstance(QueryInterface::class, 'foo');
        $this->assertInstanceOf(QueryInterface::class, $foo);
        $result = $foo([]);
        $this->assertSame('https://httpbin.org/anything/foo', $result['url']);
    }

    public function testCallable()
    {
        $foo = (new Injector($this->module))->getInstance('', 'foo');
        $this->assertInternalType('callable', $foo);
        $result = $foo([]);
        $this->assertSame('https://httpbin.org/anything/foo', $result['url']);
    }
}
