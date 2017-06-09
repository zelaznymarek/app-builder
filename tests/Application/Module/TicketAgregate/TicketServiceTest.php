<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TicketAggregate;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Model\ValueObject\Ticket;
use Pvg\Application\Module\TicketAggregate\FullTicket;
use Pvg\Application\Module\TicketAggregate\TicketService;
use Pvg\Event\Application\BitbucketTicketMappedEvent;
use Pvg\Event\Application\FullTicketBuiltEvent;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\TicketDirIndexedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Pvg\Application\Module\TicketAggregate\TicketService
 */
class TicketServiceTest extends TestCase
{
    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    public function setUp() : void
    {
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger     = $this->createMock(LoggerInterface::class);
    }

    /**
     * @test
     * @dataProvider validDataProvider
     */
    public function expectValidTicketIsCreated(
        BitbucketTicketMappedEvent $prEvent,
        JiraTicketMappedEvent $ticketEvent,
        TicketDirIndexedEvent $dirEvent
    ) : void {
        $prDataKey = array_keys($prEvent->bitbucketTicket())[0];
        /** @var Ticket */
        $ticket = new Ticket(
            $ticketEvent->ticket(),
            $prEvent->bitbucketTicket()[$prDataKey],
            $dirEvent->indexedDir()
        );

        /** @var FullTicket */
        $builder = $this
            ->getMockBuilder(FullTicket::class)
            ->setMethods(null)
            ->getMock();

        /** @var TicketService */
        $ticketService = new TicketService(
            $builder,
            $this->dispatcher,
            $this->logger
        );

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                FullTicketBuiltEvent::NAME,
                new FullTicketBuiltEvent($ticket)
            );

        $ticketService->onJiraTicketMapped($ticketEvent);
        $ticketService->onTicketDirIndexed($dirEvent);
        $ticketService->onBitbucketTicketMapped($prEvent);
    }

    public function validDataProvider() : array
    {
        $ticketData = [
            'id'                    => 20,
            'ticket_key'            => 'key',
            'assignee_name'         => 'maro',
            'assignee_display_name' => 'klaro',
            'assignee_email'        => 'email',
            'assignee_active'       => true,
            'status'                => 'Done',
            'status_category'       => 'Done',
            'components'            => 'Comps',
            'ticket_type'           => 'Fix',
            'project'               => 'project',
            'fix_version'           => '1.0',
            'summary'               => 'Summary...',
        ];
        $prData = [
            '20' => [
                'pull_request_branch'      => 'branch',
                'pull_request_last_update' => '2017',
                'pull_request_url'         => 'www.url.com',
                'pull_request_status'      => 'Merged',
                'pull_request_name'        => 'key',
                'repository'               => 'repo',
            ],
        ];
        $dirData = [
            'ticketExists' => true,
            'ticketDir'    => '/dir/id',
            'ticketId'     => 20,
        ];

        $ticketEvent = new JiraTicketMappedEvent($ticketData);
        $dirEvent    = new TicketDirIndexedEvent($dirData);
        $prEvent     = new BitbucketTicketMappedEvent($prData);

        return [
            'validData1' => [$prEvent, $ticketEvent, $dirEvent],
        ];
    }
}
