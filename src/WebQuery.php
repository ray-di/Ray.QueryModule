<?php

declare(strict_types=1);

namespace Ray\Query;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Ray\Query\Exception\WebQueryException;

final class WebQuery implements QueryInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    public function __construct(ClientInterface $client, string $method, string $uri)
    {
        $this->client = $client;
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array ...$queries) : array
    {
        $query = $queries[0];
        /** @psalm-suppress InvalidCatch */
        try {
            $response = $this->client->request($this->method, $this->uri, ['query' => $query]);
            $body = $response->getBody()->getContents();

            return json_decode($body, true);
        } catch (GuzzleException $e) {
            throw new WebQueryException($e->getMessage(), 0, $e);
        }
    }
}
