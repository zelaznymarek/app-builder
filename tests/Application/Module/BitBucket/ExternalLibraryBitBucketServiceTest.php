<?php

declare(strict_types = 1);

namespace Tests\Application\Module\BitBucket;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\BitBucket\ExternalLibraryBitBucketService;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Event\Application\BitbucketTicketMappedEvent;
use AppBuilder\Event\Application\JiraTicketMappedEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \AppBuilder\Application\Module\BitBucket\ExternalLibraryBitBucketService
 */
class ExternalLibraryBitBucketServiceTest extends TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    /** @var Parameters */
    private $applicationParams;

    /** @var Client | \PHPUnit_Framework_MockObject_MockObject */
    private $client;

    protected function setUp() : void
    {
        $this->logger            = $this->createMock(LoggerInterface::class);
        $this->dispatcher        = $this->createMock(EventDispatcherInterface::class);
        $this->applicationParams = $this->createMock(Parameters::class);
        $this->client            = $this->createMock(Client::class);
    }

    /**
     * @test
     * @dataProvider mappedPrAndResponseData
     */
    public function willDispatchEventWithMappedPullRequest(array $mappedRP, array $content) : void
    {
        $event = $this->createMock(JiraTicketMappedEvent::class);
        $event->method('ticket')->willReturn(['id' => 10]);

        /** @var ExternalLibraryHttpClient */
        $httpClient = new ExternalLibraryHttpClient($this->client, $this->applicationParams);

        /** @var ExternalLibraryBitBucketService */
        $bbService = new ExternalLibraryBitBucketService($httpClient, $this->logger, $this->dispatcher);

        /** @var StreamInterface */
        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn(json_encode($content));

        /** @var Response */
        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($body);

        $this->client->method('request')->willReturn($response);

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                BitbucketTicketMappedEvent::NAME,
                new BitbucketTicketMappedEvent($mappedRP)
            );

        $bbService->onJiraTicketMapped($event);
    }

    public function mappedPrAndResponseData() : array
    {
        $mappedPR = [
            '10' => [
                'pull_request_branch'      => 'bugfix',
                'pull_request_last_update' => '2017-06-13',
                'pull_request_url'         => 'some_url',
                'pull_request_status'      => 'Done',
                'pull_request_name'        => 'Bugfix',
                'repository'               => 'repo',
            ],
        ];

        $content = [
            'detail' => [
                [
                    'pullRequests' => [
                        [
                            'name'   => 'Bugfix',
                            'source' => [
                                'branch'     => 'bugfix',
                                'repository' => [
                                    'name' => 'repo',
                                ],
                            ],
                            'status'     => 'Done',
                            'url'        => 'some_url',
                            'lastUpdate' => '2017-06-13',
                        ],
                    ],
                ],
            ],
        ];

        return [
            'data1' => [$mappedPR, $content],
        ];
    }
}
