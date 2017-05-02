<?php

declare(strict_types=1);

namespace Pvg\Application\Jira\ValueObject;

use Pvg\Application\Jira\Exception\InvalidJiraStatusException;

class JiraTicketStatus
{
    public const ABGELEHNT         = 'Abgelehnt';
    public const APPROVED          = 'Approved';
    public const BACKLOG           = 'Backlog';
    public const CANCELLED         = 'Cancelled';
    public const CHECK_FINISHED    = 'Check finished';
    public const CHECK_IN_PROGRESS = 'Check in progress';
    public const CHECK_WAITING     = 'Check waiting';
    public const CLOSED            = 'Closed';
    public const DONE              = 'Done';
    public const ERLEDIGT          = 'Erledigt';
    public const GELOST            = 'Gelöst';
    public const IN_PROGRESS       = 'In Progress';
    public const IN_REVIEW         = 'In Review';
    public const OPEN              = 'Open';
    public const REJECTED          = 'Rejected';

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
        if (!array_key_exists($status, self::ALLOWED_STATUSES)) {
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
}
