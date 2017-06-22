<?php

declare(strict_types = 1);

namespace Tests\Application\Module\Jira;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Module\Jira\Exception\NullResultReturned;
use AppBuilder\Application\Module\Jira\ExternalLibraryJiraService;
use AppBuilder\Application\Module\Jira\QueryRepository;
use AppBuilder\Event\Application\JiraTicketMappedEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \AppBuilder\Application\Module\Jira\ExternalLibraryJiraService
 */
class ExternalLibraryJiraServiceTest extends TestCase
{
    /** @var LoggerInterface | PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var EventDispatcherInterface | PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    /** @var QueryRepository */
    private $queryRepository;

    /** @var Client | PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /** @var Parameters */
    private $applicationParams;

    protected function setUp() : void
    {
        $this->logger            = $this->createMock(LoggerInterface::class);
        $this->dispatcher        = $this->createMock(EventDispatcherInterface::class);
        $this->queryRepository   = new QueryRepository();
        $this->client            = $this->createMock(Client::class);
        $this->applicationParams = $this->createMock(Parameters::class);
    }

    /**
     * @test
     * @dataProvider mappedTicketAndResponseData
     */
    public function willDispatchValidEventAfterFetchAllTickets(array $mappedTicket, array $content) : void
    {
        /** @var ExternalLibraryHttpClient */
        $httpClient = new ExternalLibraryHttpClient($this->client, $this->applicationParams);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $httpClient,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

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
                JiraTicketMappedEvent::NAME,
                new JiraTicketMappedEvent($mappedTicket)
            );

        $jiraService->fetchAllTickets();
    }

    /**
     * @test
     * @dataProvider mappedTicketAndResponseData
     */
    public function willDispatchValidEventAfterFetchByStatus(array $mappedTicket, array $content) : void
    {
        /** @var ExternalLibraryHttpClient */
        $httpClient = new ExternalLibraryHttpClient($this->client, $this->applicationParams);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $httpClient,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

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
                JiraTicketMappedEvent::NAME,
                new JiraTicketMappedEvent($mappedTicket)
            );

        $jiraService->fetchTicketsByStatus('Done');
    }

    /**
     * @test
     */
    public function fetchByStatusWillThrowException() : void
    {
        /** @var ExternalLibraryHttpClient */
        $httpClient = new ExternalLibraryHttpClient($this->client, $this->applicationParams);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $httpClient,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('warning')
            ->withAnyParameters();

        $this->expectException(NullResultReturned::class);

        $jiraService->fetchTicketsByStatus('status');
    }

    /**
     * @test
     */
    public function fetchAllTicketsWillThrowException() : void
    {
        $this->queryRepository = $this->createMock(QueryRepository::class);

        /** @var ExternalLibraryHttpClient */
        $httpClient = new ExternalLibraryHttpClient($this->client, $this->applicationParams);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $httpClient,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        /** @var StreamInterface */
        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn('');

        /** @var Response */
        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($body);

        $this->client->method('request')->willReturn($response);

        $this->expectException(NullResultReturned::class);

        $jiraService->fetchAllTickets();
    }

    public function mappedTicketAndResponseData() : array
    {
        $mappedTicket =
            [
                'id'                    => 20,
                'ticket_key'            => 'IN-4',
                'assignee_name'         => 'name',
                'assignee_display_name' => 'display',
                'assignee_email'        => 'email',
                'assignee_active'       => true,
                'status'                => 'Done',
                'status_category'       => 'Done...',
                'components'            => 'Comps',
                'ticket_type'           => 'Fix',
                'project'               => 'project',
                'fix_version'           => '1.0',
                'summary'               => 'Summary...',
            ];

        $content = [
            'issues' => [
                [
                    'id'     => '20',
                    'key'    => 'IN-4',
                    'fields' => [
                        'assignee' => [
                            'name'         => 'name',
                            'emailAddress' => 'email',
                            'displayName'  => 'display',
                            'active'       => true,
                        ],
                        'status' => [
                            'name'           => 'Done',
                            'statuscategory' => [
                                'name' => 'Done...',
                            ],
                        ],
                        'components' => [
                            [
                                'name' => 'Comps',
                            ],
                        ],
                        'issuetype' => [
                            'name' => 'Fix',
                        ],
                        'project' => [
                            'name' => 'project',
                        ],
                        'fixVersions' => [
                            [
                                'name' => '1.0',
                            ],
                        ],
                        'summary' => 'Summary...',
                    ],
                ],
            ],
        ];

        return [
            'data1' => [$mappedTicket, $content],
        ];
    }
}
