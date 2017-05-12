<?php

declare(strict_types=1);

namespace Tests\Application\Module\Jira;

use JiraRestApi\Issue\IssueService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\ExternalLibraryJiraService;
use Pvg\Application\Module\Jira\QueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @coversNothing
 */
class ExternalLibraryJiraServiceTest extends TestCase
{
    /** @var IssueService */
    private $jiraService;
    /** @var LoggerInterface */
    private $logger;
    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;
    /** @var QueryRepository */
    private $queryRepository;
    /** @var ExternalLibraryJiraService */
    private $service;
    /** @var array */
    private $data;

    public function setUp() : void
    {
        $this->jiraService       = $this->createMock(IssueService::class);
        $this->logger            = $this->createMock(LoggerInterface::class);
        $this->dispatcher        = $this->createMock(EventDispatcherInterface::class);
        $this->queryRepository   = new QueryRepository();
        $this->service           = new ExternalLibraryJiraService(
            $this->jiraService,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );

        $this->data = [
            'IN-4' => [
                'fields' => [
                    'assignee' => [
                        'active'       => true,
                        'displayName'  => 'Steffen Stundzig',
                        'emailAddress' => 'steffen.stundzig@sstit.de',
                        'name'         => 'steffen',
                    ],
                    'components' => [
                        'name' => 'Jira, Confluence, Bitbucket',
                    ],
                    'status' => [
                        'name'           => 'In Progress',
                        'statuscategory' => [
                            'name' => 'In Progress...',
                        ],
                    ],
                    'summary' => 'Konfiguration Support Hotline',
                ],
                'id'  => '10008',
                'key' => 'IN-4',
            ],
        ];
    }

    /**
     * @test
     */
    public function map() : void
    {
        $result = $this->service->mapToJiraTicket($this->data);
        $this->assertInternalType('array', $result);
        $this->assertInternalType('array', $result['IN-4']);
        $this->assertInternalType('array', $result['IN-4']['assignee']);
        $this->assertTrue($result['IN-4']['assignee']['active']);
        $this->assertSame('steffen', $result['IN-4']['assignee']['name']);
        $this->assertSame('Jira, Confluence, Bitbucket', $result['IN-4']['components']);
        $this->assertSame('In Progress', $result['IN-4']['status']);
        $this->assertSame('In Progress...', $result['IN-4']['status_category']);
        $this->assertSame('Konfiguration Support Hotline', $result['IN-4']['summary']);
        $this->assertSame(10008, $result['IN-4']['id']);
        $this->assertSame('IN-4', $result['IN-4']['ticket_key']);
    }

    /*
        /**
         * @test
         */
    /*
    public function login() : void
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $this->assertTrue($this->service->login());

        $this->service->onApplicationInitialized();
    }

    public function testFetchAllTickets() : void
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $this->service->fetchAllTickets();
    }
    */
}
