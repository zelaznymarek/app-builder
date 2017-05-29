<?php

declare(strict_types=1);

namespace Tests\Application\Module\Jira;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\Exception\NullResultReturned;
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
        IssueSearchResult $isr,
        array $result
    ) : void {
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

    /**
     * @test
     */
    public function expectFetchAllTicketsThrowsException() : void
    {
        $arrayConfig = new ArrayConfiguration([
            'jiraHost'     => 'host',
            'jiraUser'     => 'user',
            'jiraPassword' => 'pass',
        ]);

        /* @var \PHPUnit_Framework_MockObject_MockObject */
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

        $issueService
            ->expects($this->once())
            ->method('search')
            ->willThrowException(new JiraException());

        $this->expectExceptionMessage('Error. Fetching method returned null');

        $jiraService->fetchAllTickets();
    }

    /**
     * @test
     */
    public function onApplicationInitializedThrowsException() : void
    {
        $arrayConfig = new ArrayConfiguration([
            'jiraHost'     => 'host',
            'jiraUser'     => 'user',
            'jiraPassword' => 'pass',
        ]);

        $this->issueService = new IssueService($arrayConfig);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $this->issueService,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('warning')
            ->with('Invalid login or password');

        $jiraService->onApplicationInitialized();
    }

    /**
     * @test
     */
    public function fetchTicketsByStatusthrowsException() : void
    {
        $this->issueService = $this->createMock(IssueService::class);

        /** @var ExternalLibraryJiraService */
        $jiraService = new ExternalLibraryJiraService(
            $this->issueService,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this->expectException(NullResultReturned::class);

        $jiraService->fetchTicketsByStatus('InvalidStatus');
    }

    public function issueServiceDataProvider() : array
    {
        $issue         = new Issue();
        $issue->key    = 'TEST';
        $issue->id     = '10';
        $issue->fields = [
            'assignee' => [
                'name'   => 'marek',
                'active' => true,
            ],
            'status' => [
                'name'           => 'Done',
                'statuscategory' => [
                    'name' => 'Done...',
                    ],
            ],
            'summary' => 'Test summary',
        ];

        $issueSearchResult = new IssueSearchResult();
        $issueSearchResult->setIssues([$issue]);

        $result = [
                'id'                    => 10,
                'ticket_key'            => 'TEST',
                'assignee_name'         => 'marek',
                'assignee_active'       => true,
                'status'                => 'Done',
                'status_category'       => 'Done...',
                'summary'               => 'Test summary',
                'assignee_email'        => '',
                'assignee_display_name' => '',
                'components'            => '',
                'ticket_type'           => '',
                'project'               => '',
                'fix_version'           => '',
        ];

        return [
            'data1' => [$issueSearchResult, $result],
        ];
    }
}
