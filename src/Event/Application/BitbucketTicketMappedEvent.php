<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class BitbucketTicketMappedEvent extends Event
{
    /** @var string */
    public const NAME = 'bitbucket.ticked.mapped';

    /** @var array */
    private $bitbucketTicket;

    public function __construct(array $ticket)
    {
        $this->bitbucketTicket = $ticket;
    }

    public function bitbucketTicket() : array
    {
        return $this->bitbucketTicket;
    }
}
