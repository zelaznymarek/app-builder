<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use JiraRestApi\Issue\IssueSearchResult;
use Symfony\Component\EventDispatcher\Event;

class TicketsFetchedEvent extends Event
{
    /** @var string */
    public const NAME = 'tickets.fetched';

    /** @var IssueSearchResult */
    private $tickets;

    public function __construct(IssueSearchResult $tickets)
    {
        $this->tickets = $tickets;
    }

    public function tickets() : IssueSearchResult
    {
        return $this->tickets;
    }
}
