<?php

declare(strict_types=1);

namespace Pvg\Application\Module\BitBucket;

use GuzzleHttp\Client;

/**
 * Class combines Guzzle Client object and an array with credentials for further convenience.
 */
class GuzzleClient
{
    /** @var Client */
    private $client;

    /** @var string */
    private $user;

    /** @var string */
    private $password;

    public function __construct(Client $client, array $credentials)
    {
        $this->client   = $client;
        $this->user     = $credentials['user'];
        $this->password = $credentials['password'];
    }

    /**
     * @return Client
     */
    public function client() : Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function user() : string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function password() : string
    {
        return $this->password;
    }
}
