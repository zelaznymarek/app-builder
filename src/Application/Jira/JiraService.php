<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

interface JiraService
{
    /**
     * Method uses provided credentials to connect JIRA.
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
