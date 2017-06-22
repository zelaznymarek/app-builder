<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\HttpClient;

use GuzzleHttp\Psr7\Response;

interface HttpClient
{
    public function request(string $method, string $url) : Response;
}
