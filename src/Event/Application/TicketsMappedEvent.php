<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class TicketsMappedEvent extends Event
{
    /** @var string */
    public const NAME = 'tickets.mapped';

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
