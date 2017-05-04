<?php

declare(strict_types=1);

namespace Pvg\Application\Jira\ValueObject;

use Pvg\Application\Jira\Exception\InvalidJiraStatusException;

class JiraTicketStatus
{
    private const ABGELEHNT         = 'Abgelehnt';
    private const APPROVED          = 'Approved';
    private const BACKLOG           = 'Backlog';
    private const CANCELLED         = 'Cancelled';
    private const CHECK_FINISHED    = 'Check finished';
    private const CHECK_IN_PROGRESS = 'Check in progress';
    private const CHECK_WAITING     = 'Check waiting';
    private const CLOSED            = 'Closed';
    private const DONE              = 'Done';
    private const ERLEDIGT          = 'Erledigt';
    private const GELOST            = 'Gelöst';
    private const IN_PROGRESS       = 'In Progress';
    private const IN_REVIEW         = 'In Review';
    private const OPEN              = 'Open';
    private const REJECTED          = 'Rejected';

    private const ALLOWED_STATUSES = [
        self::ABGELEHNT,
        self::APPROVED,
        self::BACKLOG,
        self::CANCELLED,
        self::CHECK_FINISHED,
        self::CHECK_IN_PROGRESS,
        self::CHECK_WAITING,
        self::CLOSED,
        self::DONE,
        self::ERLEDIGT,
        self::GELOST,
        self::IN_PROGRESS,
        self::IN_REVIEW,
        self::OPEN,
        self::REJECTED,
    ];

    /** @var string */
    private $status;

    public function __construct(string $status)
    {
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            throw new InvalidJiraStatusException("Status $status not found");
        }
        $this->status = $status;
    }

    /**
     * Creates JiraTicketStatus value object with given status.
     */
    public static function createFromString(string $status) : self
    {
        return new self($status);
    }

    public function status() : string
    {
        return $this->status;
    }
}
