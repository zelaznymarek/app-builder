<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class TicketsFetchedEvent extends Event
{
    /** @var string */
    public const NAME = 'tickets.fetched';

    /** @var array */
    private $tickets;

    public function __construct(array $tickets)
    {
        $this->tickets = $tickets;
    }

    public function tickets() : array
    {
        return $this->tickets;
    }
}
