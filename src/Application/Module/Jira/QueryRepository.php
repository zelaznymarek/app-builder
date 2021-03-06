<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\Jira;

class QueryRepository
{
    private const VALIDATE_CREDENTIALS_QUERY    = 'createdDate > now()';
    private const FETCH_ALL_TICKETS_QUERY       = 'status in '
        . "('Work finished', 'Work in progress') "
        . "OR (status = Done AND status changed AFTER startOfDay('-24h')) "
        . 'ORDER BY key ASC';

    private const FETCH_TICKETS_BY_STATUS_QUERY = 'status = "%s"';
    private const FETCH_PARTICULAR_TICKET       = 'key = %s';

    public function validateCredentials() : string
    {
        return static::VALIDATE_CREDENTIALS_QUERY;
    }

    public function fetchAllTickets() : string
    {
        return static::FETCH_ALL_TICKETS_QUERY;
    }

    public function fetchTicketsByStatus(string $status) : string
    {
        return sprintf(static::FETCH_TICKETS_BY_STATUS_QUERY, $status);
    }

    public function fetchParticularTicket(string $key) : string
    {
        return sprintf(static::FETCH_PARTICULAR_TICKET, $key);
    }
}
