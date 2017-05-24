<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

use Psr\Log\LoggerInterface;
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
        $this->bbCompleted = false;
        $this->logger->info('BB ticket: ' . key($event->bitbucketTicket()));
        $event->stopPropagation();
        foreach ($event->bitbucketTicket() as $key => $value) {
            $this->bitBucketData = $value;
        }
        $this->buildBitbucketPart();
    }

    /**
     * Uses data array passed in event to build JIRA part of ticket.
     */
    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $this->jiraCompleted = false;
        $this->logger->info('Jira ticket: ' . $event->ticket()['id']);
        $event->stopPropagation();
        $this->jiraData = $event->ticket();
        $this->buildJiraPart();
    }

    /**
     * Uses data array passed in event to build part of ticket with local directory info.
     */
    public function onTicketDirIndexed(TicketDirIndexedEvent $event) : void
    {
        $this->dirCompleted = false;
        $this->logger->info('Dir ticket: ' . $event->indexedDir()['ticketId']);
        $event->stopPropagation();
        $this->dirData = $event->indexedDir();
        $this->buildDirectoryPart();
    }

    /**
     * Builds JIRA part of ticket and sets its flag for true.
     */
    private function buildJiraPart() : void
    {
        $this->builder->addId($this->jiraData['id']);
        $this->builder->addKey($this->jiraData['ticket_key']);
        $this->builder->addAssigneeName($this->jiraData['assignee_name']);
        $this->builder->addAssigneeDisplayName($this->jiraData['assignee_display_name']);
        $this->builder->addAssigneeEmail($this->jiraData['assignee_email']);
        $this->builder->addIsAssigneeActive($this->jiraData['assignee_active']);
        $this->builder->addTicketStatus($this->jiraData['status']);
        $this->builder->addTicketStatusCategory($this->jiraData['status_category']);
        $this->builder->addComponents($this->jiraData['components']);
        $this->builder->addType($this->jiraData['ticket_type']);
        $this->builder->addProject($this->jiraData['project']);
        $this->builder->addFixVersion($this->jiraData['fix_version']);
        $this->builder->addSummary($this->jiraData['summary']);
        $this->jiraCompleted = true;
        ++$this->parts;
        $this->logger->info($this->parts . '/3 parts built');
        $this->getTicket();
    }

    /**
     * Builds BitBucket part of ticket and sets its flag for true.
     */
    private function buildBitbucketPart() : void
    {
        $this->builder->addBranch($this->bitBucketData['pull_request_branch']);
        $this->builder->addLastUpdate($this->bitBucketData['pull_request_last_update']);
        $this->builder->addUrl($this->bitBucketData['pull_request_url']);
        $this->builder->addPullRequestStatus($this->bitBucketData['pull_request_status']);
        $this->builder->addPullRequestName($this->bitBucketData['pull_request_name']);
        ++$this->parts;
        $this->logger->info($this->parts . '/3 parts built');
        $this->bbCompleted = true;
        $this->getTicket();
    }

    /**
     * Builds local directory info part of ticket and sets its flag for true.
     */
    private function buildDirectoryPart() : void
    {
        $this->builder->addHasDirectory($this->dirData['ticketExists']);
        $this->builder->addDirectory($this->dirData['ticketDir']);
        ++$this->parts;
        $this->logger->info($this->parts . '/3 parts built');
        $this->dirCompleted = true;
        $this->getTicket();
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
        if ($this->ticketIsComplete()) {
            $this->ticket = $this->builder->ticket();
            $this->logger->info('Ticket id: ' . $this->ticket->id() . ' created');
            $this->dispatcher->dispatch(FullTicketBuiltEvent::$NAME,
                new FullTicketBuiltEvent($this->ticket));
        }
    }
}
