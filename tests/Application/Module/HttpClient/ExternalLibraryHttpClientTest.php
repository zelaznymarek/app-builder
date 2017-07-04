<?php

declare(strict_types = 1);

namespace Tests\Application\Module\HttpClient;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient
 */
class ExternalLibraryHttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionThrownCauseOfInvalidCredentials() : void
    {
        /** @var Client */
        $client = new Client(['base_uri' => 'https://farpoint.get-ag.com/jira']);

        /** @var Parameters */
        $applicationParams = $this->createMock(Parameters::class);

        $httpClient = new ExternalLibraryHttpClient(
            $client,
            $applicationParams
        );

        $this->expectException(ClientException::class);

        $httpClient->request('GET', 'url');
    }

    /**
     * @test
     */
    public function exceptionThrownCauseOfInvalidHost() : void
    {
        /** @var Client */
        $client = new Client(['base_uri' => 'host']);

        /** @var Parameters */
        $applicationParams = $this->createMock(Parameters::class);

        $httpClient = new ExternalLibraryHttpClient(
            $client,
            $applicationParams
        );

        $this->expectException(ConnectException::class);

        $httpClient->request('GET', 'url');
    }
}
