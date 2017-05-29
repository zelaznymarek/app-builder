<?php

declare(strict_types=1);

namespace Pvg\Application\Module\HttpClient;

use GuzzleHttp\Psr7\Response;

interface HttpClient
{
    public function request(string $method, string $url) : Response;
}
