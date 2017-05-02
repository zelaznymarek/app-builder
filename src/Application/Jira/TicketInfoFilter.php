<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

use JiraRestApi\Issue\IssueSearchResult;
use Pvg\Event\Application\TicketsFetchedEvent;
use Pvg\Event\Application\TicketsFetchedEventAware;

class TicketInfoFilter implements TicketsFetchedEventAware
{
    /** @var IssueSearchResult */
    private $tickets;

    public function onTicketsFetched(TicketsFetchedEvent $event) : void
    {
        $this->tickets = $event->tickets();
    }
}
