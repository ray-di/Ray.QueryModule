<?php

declare(strict_types=1);

namespace Ray\Query;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Ray\Query\Exception\WebQueryException;

use function assert;
use function is_iterable;
use function json_decode;

final class WebQuery implements QueryInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $method;

    /** @var string */
    private $uri;

    public function __construct(ClientInterface $client, string $method, string $uri)
    {
        $this->client = $client;
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * @param array<string, mixed> ...$queries
     */
    public function __invoke(array ...$queries): iterable
    {
        $query = $queries[0];
        try {
            $response = $this->client->request($this->method, $this->uri, ['query' => $query]);
            $body = $response->getBody()->getContents();
            $array = json_decode($body, true);
            assert(is_iterable($array));

            return $array;
        } catch (GuzzleException $e) {
            throw new WebQueryException($e->getMessage(), 0, $e);
        }
    }
}
