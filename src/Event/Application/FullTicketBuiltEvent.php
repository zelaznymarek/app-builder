<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use Pvg\Application\Model\ValueObject\Ticket;
use Symfony\Component\EventDispatcher\Event;

class FullTicketBuiltEvent extends Event
{
    /** @var string */
    public const NAME = 'full.ticket.built';

    /** @var Ticket */
    private $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function ticket() : Ticket
    {
        return $this->ticket;
    }
}
