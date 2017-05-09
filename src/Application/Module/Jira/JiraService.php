<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira;

interface JiraService
{
    /**
     * Returns true if credentials are validated successfully. Throws exception if not.
     */
    public function login() : bool;

    /**
     *  Method fetches tickets with given status and dispatches event.
     */
    public function fetchTicketsByStatus(string $status) : void;

    /**
     * Method fetches all tickets and dispatches event.
     */
    public function fetchAllTickets() : void;
}
