<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TicketAggregate;

use AppBuilder\Application\Model\ValueObject\Ticket;

class FullTicket implements TicketBuilder
{
    /** @var array */
    private $ticketData;

    /** @var array */
    private $prData;

    /** @var array */
    private $dirData;

    public function addTicketData(array $ticketData) : void
    {
        $this->ticketData = $ticketData;
    }

    public function addPullRequestData(array $prData) : void
    {
        $this->prData = $prData;
    }

    public function addDirectoryData(array $dirData) : void
    {
        $this->dirData = $dirData;
    }

    /**
     * Returns ticket VO created from jira ticket, pull request and directory data.
     */
    public function ticket() : Ticket
    {
        return new Ticket(
            $this->ticketData,
            $this->prData,
            $this->dirData
        );
    }
}
