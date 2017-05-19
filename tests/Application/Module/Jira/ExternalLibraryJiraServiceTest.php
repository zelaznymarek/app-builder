<?php

declare(strict_types=1);

namespace Tests\Application\Module\Jira;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\ExternalLibraryJiraService;
use Pvg\Application\Module\Jira\QueryRepository;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Pvg\Application\Module\Jira\ExternalLibraryJiraService
 */
class ExternalLibraryJiraServiceTest extends TestCase
{
    /** @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $logger;
    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;
    /** @var QueryRepository */
    private $queryRepository;
    /** @var IssueService */
    private $issueService;

    public function setUp() : void
    {
        $this->logger          = $this->createMock(LoggerInterface::class);
        $this->dispatcher      = $this->createMock(EventDispatcherInterface::class);
        $this->queryRepository = new QueryRepository();
    }

    /**
     * @test
     */
    public function validateCredentialsReturnsFalse() : void
    {
        /** @var IssueService */
        $issueService = new IssueService(
            new ArrayConfiguration([
                'jiraHost'     => 'host',
                'jiraUser'     => 'user',
                'jiraPassword' => 'pass',
            ]));

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $issueService,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this->assertFalse($jiraService->validateCredentials());
    }

    /**
     * @test
     */
    public function validateCredentialsReturnsTrue() : void
    {
        /** @var IssueService */
        $issueService = $this->createMock(IssueService::class);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $issueService,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this->assertTrue($jiraService->validateCredentials());
    }

    /**
     * @test
     * @dataProvider issueServiceDataProvider
     */
    public function expectDispatchedEventContainsMappedIssue(
        array $ticket,
        array $result
    ) : void {
        /** @var IssueSearchResult */
        $isr = $ticket;

        $arrayConfig = new ArrayConfiguration([
            'jiraHost'     => 'host',
            'jiraUser'     => 'user',
            'jiraPassword' => 'pass',
        ]);

        /* @var \PHPUnit_Framework_MockObject_MockObject issueService */
        $this->issueService = $this->getMockBuilder(IssueService::class)
            ->setConstructorArgs([$arrayConfig])
            ->setMethods(['search'])
            ->getMock();

        /** @var IssueService */
        $issueService = $this->issueService;

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $issueService,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this->issueService
            ->expects($this->once())
            ->method('search')
            ->willReturn($isr);

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(JiraTicketMappedEvent::NAME,
                new JiraTicketMappedEvent($result));

        $jiraService->fetchAllTickets();
    }

    public function issueServiceDataProvider() : array
    {
        /** TODO Build stdClass similar to IssueSearchResult
         *
         */
        $issue        = new \stdClass();
        $issue->array = [
            [
                'id'     => '10',
                'key'    => 'TEST',
                'fields' => [
                    'assignee' => [
                        'name'   => 'marek',
                        'active' => true,
                    ],
                    'status' => [
                        'name'            => 'Done',
                        'status_category' => 'Done...',
                    ],
                ],
                'summary' => 'Test summary',
            ],
        ];

        $result = [
            'TEST' => [
                'id'              => 10,
                'ticket_key'      => 'TEST',
                'assignee_name'   => 'marek',
                'assignee_active' => true,
                'status'          => 'Done',
                'status_category' => 'Done...',
                'summary'         => 'Test summary',
            ],
        ];

        return [
            'data1' => [$issue->array, $result],
        ];
    }
}
