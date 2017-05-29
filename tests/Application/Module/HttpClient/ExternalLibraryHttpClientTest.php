<?php


namespace Tests\Application\Module\HttpClient;



use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;
use Pvg\Application\Module\HttpClient\ExternalLibraryHttpClient;

/**
 * @covers \Pvg\Application\Module\HttpClient\ExternalLibraryHttpClient
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

        $httpClient = new ExternalLibraryHttpClient(
            $client,
            [
                'user'     => 'user',
                'password' => 'pass',
            ]
        );

        $this->expectException(ClientException::class);

        $httpClient->request('GET','url');
    }

    /**
     * @test
     */
    public function exceptionThrownCauseOfInvalidHost() : void
    {
        /** @var Client */
        $client = new Client(['base_uri' => 'host']);

        $httpClient = new ExternalLibraryHttpClient(
            $client,
            [
                'user'     => 'user',
                'password' => 'pass',
            ]
        );

        $this->expectException(ConnectException::class);

        $httpClient->request('GET','url');
    }
}
