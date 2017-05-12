<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira;

class Ticket
{
    /** @var string */
    private $assigneeName;

    /** @var string */
    private $assigneeDisplayName;

    /** @var string */
    private $assigneeEmail;

    /** @var bool */
    private $assigneeIsActive;

    /** @var int */
    private $id;

    /** @var string */
    private $issueType;

    /** @var string */
    private $fixVersion;

    /** @var string */
    private $projectName;

    /** @var string */
    private $summary;

    /** @var string */
    private $statusName;

    /** @var string */
    private $statusCategory;

    /** @var string */
    private $ticketName;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function assigneeName() : string
    {
        return $this->assigneeName;
    }

    /**
     * @return string
     */
    public function assigneeDisplayName() : string
    {
        return $this->assigneeDisplayName;
    }

    /**
     * @return string
     */
    public function assigneeEmail() : string
    {
        return $this->assigneeEmail;
    }

    /**
     * @return bool
     */
    public function assigneeIsActive() : bool
    {
        return $this->assigneeIsActive;
    }

    /**
     * @return int
     */
    public function id() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function issueType() : string
    {
        return $this->issueType;
    }

    /**
     * @return string
     */
    public function fixVersion() : string
    {
        return $this->fixVersion;
    }

    /**
     * @return string
     */
    public function projectName() : string
    {
        return $this->projectName;
    }

    /**
     * @return string
     */
    public function summary() : string
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function statusName() : string
    {
        return $this->statusName;
    }

    /**
     * @return string
     */
    public function statusCategory() : string
    {
        return $this->statusCategory;
    }

    /**
     * @return string
     */
    public function ticketName() : string
    {
        return $this->ticketName;
    }
}
