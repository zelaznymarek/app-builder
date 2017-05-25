<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

use Psr\Log\LoggerInterface;
use Pvg\Application\Model\Exception\NullArgumentException;
use Pvg\Application\Model\Ticket;
use Pvg\Event\Application\BitbucketTicketMappedEvent;
use Pvg\Event\Application\BitbucketTicketMappedEventAware;
use Pvg\Event\Application\FullTicketBuiltEvent;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEventAware;
use Pvg\Event\Application\TicketDirIndexedEvent;
use Pvg\Event\Application\TicketDirIndexedEventAware;
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
        $event->stopPropagation();
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
        $event->stopPropagation();
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
        $event->stopPropagation();
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
        $this->logger->info(
            'Ticket '
            . $this->currentId
            . ': '
            . $this->parts
            . '/3 parts built');
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
                FullTicketBuiltEvent::$NAME,
                new FullTicketBuiltEvent($this->ticket)
            );
        } catch (NullArgumentException $e) {
            $this->logger->warning($e->getMessage());
        }
    }
}
