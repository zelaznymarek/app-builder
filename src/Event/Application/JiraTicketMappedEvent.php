<?php

declare(strict_types = 1);

namespace Pvg\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class JiraTicketMappedEvent extends Event
{
    /** @var string */
    public const NAME = 'jira.ticket.mapped';

    /** @var array */
    private $ticket;

    public function __construct(array $ticket)
    {
        $this->ticket = $ticket;
    }

    public function ticket() : array
    {
        return $this->ticket;
    }
}
