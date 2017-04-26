<?php

declare(strict_types=1);

namespace Tests\Application\Jira;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Jira\ExternalLibraryJiraService;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryJiraServiceTest extends TestCase
{
    private $logger;
    private $dispatcher;
    private $service;

    public function setUp() : void
    {
        $configArray = [
            'parameters' => [
                'jira.host'                    => 'host',
                'jira.authentication.username' => 'marek',
                'jira.authentication.password' => 'pass',
            ],
        ];
        $this->logger     = $this->createMock(LoggerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->service    = new ExternalLibraryJiraService($configArray, $this->logger, $this->dispatcher);
    }

    public function testLogin() : void
    {
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->service->login();
    }

    public function textOnApplicationInitialized() : void
    {
        $event = $this->createMock(ApplicationInitializedEvent::class);
    }
}
