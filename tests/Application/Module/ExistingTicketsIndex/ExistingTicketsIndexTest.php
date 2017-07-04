<?php

declare(strict_types = 1);

namespace Tests\Application\Module\ExistingTicketsIndex;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\ExistingTicketsIndex\ExistingTicketsIndexService;
use AppBuilder\Event\Application\JiraTicketMappedEvent;
use AppBuilder\Event\Application\TicketDirIndexedEvent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \AppBuilder\Application\Module\ExistingTicketsIndex\ExistingTicketsIndexService
 */
class ExistingTicketsIndexTest extends TestCase
{
    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp() : void
    {
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger     = $this->createMock(LoggerInterface::class);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function dispatchesEventWithExpectedData(JiraTicketMappedEvent $event, array $result) : void
    {
        $applicationParams = $this->createMock(Parameters::class);

        $applicationParams->method('projectsHomeDir')->willReturn('/');
        $applicationParams->method('path')->willReturn('/var');

        $directoryService = new ExistingTicketsIndexService(
            $applicationParams,
            $this->dispatcher,
            $this->logger
        );

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                TicketDirIndexedEvent::NAME,
                new TicketDirIndexedEvent($result)
            );

        $directoryService->onJiraTicketMapped($event);
    }

    public function dataProvider() : array
    {
        return [
            'data1' => [
                new JiraTicketMappedEvent([
                    'id'         => 10,
                    'ticket_key' => 'var',
                ]),
                [
                    'ticketId'     => 10,
                    'ticketDir'    => '/var',
                    'ticketExists' => true,
                ],
            ],
            'data2' => [
                new JiraTicketMappedEvent([
                    'id'         => 20,
                    'ticket_key' => 'someKey',
                ]),
                [
                    'ticketId'     => 20,
                    'ticketDir'    => null,
                    'ticketExists' => false,
                ],
            ],
        ];
    }
}
