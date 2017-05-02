<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

class QueryRepository
{
    private const VALIDATE_CREDENTIALS_QUERY    = 'createdDate > now()';
    private const FETCH_ALL_TICKETS_QUERY       = '';
    private const FETCH_TICKETS_BY_STATUS_QUERY = 'status = %s';

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
}
