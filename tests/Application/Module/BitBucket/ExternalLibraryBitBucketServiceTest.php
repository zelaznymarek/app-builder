<?php


namespace Tests\Application\Module\BitBucket;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\BitBucket\ExternalLibraryBitBucketService;
use Pvg\Application\Module\BitBucket\GuzzleClient;
use Pvg\Event\Application\BitbucketTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryBitBucketServiceTest extends TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface | PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @test
     * @dataProvider eventAndMappedTicketProvider
     */

//    public function expectsEventDispatchedWithMappedTicket(
//        string $bbTicket,
//        array $mappedTicket
//    ): void
//    {
//
//        /** @var  GuzzleClient */
//
//        $client = $this->createMock(Client::class);
//        $guzzleClient = new GuzzleClient($client, ['user' => 'ja', 'password' => 'pass']);
//
//        $client
//            ->expects($this->once())
//            ->method('request')
//            ->willReturn(new Response());
//
//        $bbService = new ExternalLibraryBitBucketService(
//            $guzzleClient,
//            $this->logger,
//            $this->dispatcher
//        );
//
//        $eventArray = [
//            'id' => 10
//        ];
//
//        $event = new JiraTicketMappedEvent($eventArray);
//
//        $stream = $this->createMock(Stream::class);
//
//        $stream
//            ->expects($this->once())
//            ->method('getContents')
//            ->willReturn($bbTicket);
//
//        $this
//            ->dispatcher
//            ->expects($this->once())
//            ->method('dispatch')
//            ->with([BitbucketTicketMappedEvent::NAME,
//                new BitbucketTicketMappedEvent($mappedTicket)]);
//
//        $bbService->onJiraTicketMapped($event);
//    }

    public function eventAndMappedTicketProvider(): array
    {
        $bbTicket = '{"detail":[{"pullRequests":[{"lastUpdate":"Test Date","name":"Test Name",'
            . '"source":{"branch":"Test Branch",},"status":"Test Status","url":"Test Url"}]}]}';

        $mappedTicket = [
            '10' => [
                'pull_request_name' => 'Test Name',
                'pull_request_url' => 'Test Url',
                'pull_request_status' => 'Test Status',
                'pull_request_last_update' => 'Test Date',
                'pull_request_source' => 'Test Branch'
            ]
        ];

        return [
            'data1' => [
                $bbTicket,
                $mappedTicket
            ]
        ];
    }
}
