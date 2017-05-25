<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

use Pvg\Application\Model\Ticket;

interface TicketBuilder
{
    public function addTicketData(array $ticketData) : void;

    public function addPullRequestData(array $prData) : void;

    public function addDirectoryData(array $dirData) : void;

    public function ticket() : Ticket;
}
