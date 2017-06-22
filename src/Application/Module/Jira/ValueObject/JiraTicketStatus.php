<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\Jira\ValueObject;

use AppBuilder\Application\Module\Jira\Exception\InvalidJiraStatusException;

class JiraTicketStatus
{
    public const ABGELEHNT                = 'Abgelehnt';
    public const APPROVED                 = 'Approved';
    public const BACKLOG                  = 'Backlog';
    public const CANCELLED                = 'Cancelled';
    public const CHECK_FINISHED           = 'Check finished';
    public const CHECK_IN_PROGRESS        = 'Check in progress';
    public const CHECK_WAITING            = 'Check waiting';
    public const CLOSED                   = 'Closed';
    public const DONE                     = 'Done';
    public const ERLEDIGT                 = 'Erledigt';
    public const GELOST                   = 'Gelöst';
    public const IN_PROGRESS              = 'In Progress';
    public const IN_REVIEW                = 'In Review';
    public const OPEN                     = 'Open';
    public const REJECTED                 = 'Rejected';
    public const REOPENED                 = 'Reopened';
    public const RESOLVED                 = 'Resolved';
    public const REVIEW_FINISHED          = 'Review finished';
    public const REVIEW_IN_PROGRESS       = 'Review in progress';
    public const SELECTED_FOR_DEVELOPMENT = 'Selected for development';
    public const TEST_FINISHED            = 'Test finished';
    public const TEST_IN_PROGRESS         = 'Test in progress';
    public const TO_DO                    = 'To do';
    public const UNDER_REVIEW             = 'Under review';
    public const WAITING                  = 'Waiting';
    public const WARTET_AUF_ANNAHME       = 'wartet auf Annahme';
    public const WARTET_AUF_DEN_SUPPORT   = 'Wartet auf den Support';
    public const WARTET_AUF_KUNDEN        = 'Wartet auf Kunden';
    public const WARTET_AUF_SUPPORT       = 'Wartet auf Support';
    public const WORK_FINISHED            = 'Work finished';
    public const WORK_IN_PROGRESS         = 'Work in progress';

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
        self::REOPENED,
        self::RESOLVED,
        self::REVIEW_FINISHED,
        self::REVIEW_IN_PROGRESS,
        self::SELECTED_FOR_DEVELOPMENT,
        self::TEST_FINISHED,
        self::TEST_IN_PROGRESS,
        self::TO_DO,
        self::UNDER_REVIEW,
        self::WAITING,
        self::WARTET_AUF_ANNAHME,
        self::WARTET_AUF_DEN_SUPPORT,
        self::WARTET_AUF_KUNDEN,
        self::WARTET_AUF_SUPPORT,
        self::WORK_FINISHED,
        self::WORK_IN_PROGRESS,
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
