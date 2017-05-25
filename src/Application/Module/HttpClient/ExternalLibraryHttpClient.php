<?php

declare(strict_types=1);

namespace Pvg\Application\Module\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Class combines Guzzle Client object and an array with credentials for further convenience.
 */
class ExternalLibraryHttpClient implements HttpClient
{
    /** @var string */
    public const GET = 'GET';

    /** @var Client */
    private $client;

    /** @var array */
    private $headerConfig;

    public function __construct(Client $client, array $credentials)
    {
        $this->client       = $client;
        $this->headerConfig = [
            'auth'    => [$credentials['user'], $credentials['password']],
            'headers' => ['Accept' => 'application/json'],
        ];
    }

    public function client() : Client
    {
        return $this->client;
    }

    /**
     * Sends a request with passed method and url.
     */
    public function request(string $method, string $url) : Response
    {
        return $this->client->request($method, $url, $this->headerConfig);
    }
}
