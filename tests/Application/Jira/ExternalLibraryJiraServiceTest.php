<?php

declare(strict_types=1);

namespace Tests\Application\Jira;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Jira\ExternalLibraryJiraService;
use Pvg\Application\Jira\Factory\JiraConfigFactory;
use Pvg\Application\Jira\QueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @coversNothing
 */
class ExternalLibraryJiraServiceTest extends TestCase
{
    /** @var JiraConfigFactory */
    private $jiraConfigFactory;
    /** @var LoggerInterface */
    private $logger;
    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;
    /** @var QueryRepository */
    private $queryRepository;
    /** @var ExternalLibraryJiraService */
    private $service;

    public function setUp() : void
    {
        $this->jiraConfigFactory = $this->createMock(JiraConfigFactory::class);
        $this->logger            = $this->createMock(LoggerInterface::class);
        $this->dispatcher        = $this->createMock(EventDispatcherInterface::class);
        $this->queryRepository   = new QueryRepository();
        $this->service           = new ExternalLibraryJiraService(
            $this->jiraConfigFactory,
            $this->logger,
            $this->dispatcher,
            $this->queryRepository
        );
    }

    public function testLogin() : void
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
}
