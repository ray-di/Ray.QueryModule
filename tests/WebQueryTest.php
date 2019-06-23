<?php

declare(strict_types=1);

namespace Ray\Query;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Ray\Query\Exception\WebQueryException;

class WebQueryTest extends TestCase
{
    /**
     * @var WebQuery
     */
    private $webQuery;

    protected function setUp() : void
    {
        $this->webQuery = new WebQuery(new Client, 'GET', 'https://httpbin.org/json');
    }

    public function test__invoke()
    {
        $webQuery = new WebQuery(new Client, 'GET', 'https://httpbin.org/json');
        $result = $webQuery([]);
        $this->assertArrayHasKey('slideshow', (array) $result);
    }

    public function test404()
    {
        $this->expectException(WebQueryException::class);
        $webQuery = new WebQuery(new Client, 'GET', 'https://httpbin.org/status/404');
        $result = $webQuery([]);
        $this->assertArrayHasKey('slideshow', (array) $result);
    }
}
