<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\HttpClient;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
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

    /** @var Parameters */
    private $applicationParams;

    public function __construct(Client $client, Parameters $applicationParams)
    {
        $this->applicationParams = $applicationParams;
        $this->client            = $client;
        $this->headerConfig      = [
            'auth'    => [$applicationParams->authenticationUser(), $applicationParams->authenticationPassword()],
            'headers' => ['Accept' => 'application/json'],
        ];
    }

    public function client() : Client
    {
        return $this->client;
    }

    public function applicationParams() : Parameters
    {
        return $this->applicationParams;
    }

    /**
     * Sends a request with passed method and url.
     */
    public function request(string $method, string $url) : Response
    {
        return $this->client->request($method, $url, $this->headerConfig);
    }
}
