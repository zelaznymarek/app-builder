<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TicketAggregate;

use AppBuilder\Application\Model\Exception\NullArgumentException;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Event\Application\BitbucketTicketMappedEvent;
use AppBuilder\Event\Application\BitbucketTicketMappedEventAware;
use AppBuilder\Event\Application\FullTicketBuiltEvent;
use AppBuilder\Event\Application\JiraTicketMappedEvent;
use AppBuilder\Event\Application\JiraTicketMappedEventAware;
use AppBuilder\Event\Application\TicketDirIndexedEvent;
use AppBuilder\Event\Application\TicketDirIndexedEventAware;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TicketService implements
    JiraTicketMappedEventAware,
    BitbucketTicketMappedEventAware,
    TicketDirIndexedEventAware
{
    /** @var array */
    private $jiraData;

    /** @var array */
    private $bitBucketData;

    /** @var array */
    private $dirData;

    /** @var TicketBuilder */
    private $builder;

    /** @var Ticket */
    private $ticket;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $jiraCompleted;

    /** @var bool */
    private $bbCompleted;

    /** @var bool */
    private $dirCompleted;

    /** @var int */
    private $parts;

    /** @var int */
    private $currentId;

    public function __construct(
        TicketBuilder $builder,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->builder       = $builder;
        $this->dispatcher    = $dispatcher;
        $this->logger        = $logger;
        $this->jiraCompleted = false;
        $this->bbCompleted   = false;
        $this->dirCompleted  = false;
        $this->parts         = 0;
    }

    /**
     * Uses data array passed in event to build BitBucket part of ticket.
     */
    public function onBitbucketTicketMapped(BitbucketTicketMappedEvent $event) : void
    {
        foreach ($event->bitbucketTicket() as $key => $value) {
            $this->bitBucketData = $value;
            $this->currentId     = $key;
        }
        ++$this->parts;
        $this->bbCompleted = true;
        $this->printCompletionInfo();
        $this->buildTicket();
    }

    /**
     * Uses data array passed in event to build JIRA part of ticket.
     */
    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $this->jiraData  = $event->ticket();
        $this->currentId = $event->ticket()['id'];
        ++$this->parts;
        $this->jiraCompleted = true;
        $this->printCompletionInfo();
        $this->buildTicket();
    }

    /**
     * Uses data array passed in event to build part of ticket with local directory info.
     */
    public function onTicketDirIndexed(TicketDirIndexedEvent $event) : void
    {
        $this->dirData   = $event->indexedDir();
        $this->currentId = $event->indexedDir()['ticketId'];
        ++$this->parts;
        $this->dirCompleted = true;
        $this->printCompletionInfo();
        $this->buildTicket();
    }

    /**
     * Calls ticketIsComplete method.
     * Then calls getTicket method if true returned.
     */
    private function buildTicket() : void
    {
        if ($this->ticketIsComplete()) {
            $this->getTicket();
        }
    }

    /**
     * Logs how many parts of ticket are already ready.
     */
    private function printCompletionInfo() : void
    {
        $this
            ->logger
            ->info(
                'Ticket '
                . $this->currentId
                . ': '
                . $this->parts
                . '/3 parts built'
            );
    }

    /**
     * Checks whether all 3 parts of ticket were built.
     */
    private function ticketIsComplete() : bool
    {
        return $this->jiraCompleted && $this->bbCompleted && $this->dirCompleted;
    }

    /**
     * Gets ticket from ticket builder when all parts are complete.
     */
    private function getTicket() : void
    {
        $this->builder->addDirectoryData($this->dirData);
        $this->builder->addPullRequestData($this->bitBucketData);
        $this->builder->addTicketData($this->jiraData);
        try {
            $this->ticket = $this->builder->ticket();
            $this->logger->info('Ticket id: ' . $this->ticket->id() . ' created');
            $this->dispatcher->dispatch(
                FullTicketBuiltEvent::NAME,
                new FullTicketBuiltEvent($this->ticket)
            );
        } catch (NullArgumentException $e) {
            $this->logger->warning($e->getMessage());
        }
    }
}
