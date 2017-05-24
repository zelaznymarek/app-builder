<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

/**
 * Class represents complete ticket.
 */
class Ticket
{
    /** @var int */
    private $id;

    /** @var string */
    private $key;

    /** @var string */
    private $assigneeName;

    /** @var string */
    private $assigneeDisplayName;

    /** @var string */
    private $assigneeEmail;

    /** @var bool */
    private $isAssigneeActive;

    /** @var string */
    private $ticketStatus;

    /** @var string */
    private $ticketStatusCategory;

    /** @var string */
    private $components;

    /** @var string */
    private $type;

    /** @var string */
    private $project;

    /** @var string */
    private $fixVersion;

    /** @var string */
    private $summary;

    /** @var string */
    private $branch;

    /** @var string */
    private $lastUpdate;

    /** @var string */
    private $url;

    /** @var string */
    private $pullRequestStatus;

    /** @var string */
    private $pullRequestName;

    /** @var bool */
    private $hasDirectory;

    /** @var string */
    private $directory;

    public function id() : int
    {
        return $this->id;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function key() : string
    {
        return $this->key;
    }

    public function setKey(string $key) : void
    {
        $this->key = $key;
    }

    public function assigneeName() : string
    {
        return $this->assigneeName;
    }

    public function setAssigneeName(string $assigneeName) : void
    {
        $this->assigneeName = $assigneeName;
    }

    public function assigneeDisplayName() : string
    {
        return $this->assigneeDisplayName;
    }

    public function setAssigneeDisplayName(string $assigneeDisplayName) : void
    {
        $this->assigneeDisplayName = $assigneeDisplayName;
    }

    public function assigneeEmail() : string
    {
        return $this->assigneeEmail;
    }

    public function setAssigneeEmail(string $assigneeEmail) : void
    {
        $this->assigneeEmail = $assigneeEmail;
    }

    public function isAssigneeActive() : bool
    {
        return $this->isAssigneeActive;
    }

    public function setIsAssigneeActive(bool $isAssigneeActive) : void
    {
        $this->isAssigneeActive = $isAssigneeActive;
    }

    public function ticketStatus() : string
    {
        return $this->ticketStatus;
    }

    public function setTicketStatus(string $ticketStatus) : void
    {
        $this->ticketStatus = $ticketStatus;
    }

    public function ticketStatusCategory() : string
    {
        return $this->ticketStatusCategory;
    }

    public function setTicketStatusCategory(string $ticketStatusCategory) : void
    {
        $this->ticketStatusCategory = $ticketStatusCategory;
    }

    public function components() : string
    {
        return $this->components;
    }

    public function setComponents(string $components) : void
    {
        $this->components = $components;
    }

    public function type() : string
    {
        return $this->type;
    }

    public function setType(string $type) : void
    {
        $this->type = $type;
    }

    public function project() : string
    {
        return $this->project;
    }

    public function setProject(string $project) : void
    {
        $this->project = $project;
    }

    public function fixVersion() : string
    {
        return $this->fixVersion;
    }

    public function setFixVersion(string $fixVersion) : void
    {
        $this->fixVersion = $fixVersion;
    }

    public function summary() : string
    {
        return $this->summary;
    }

    public function setSummary(string $summary) : void
    {
        $this->summary = $summary;
    }

    public function branch() : string
    {
        return $this->branch;
    }

    public function setBranch(string $branch) : void
    {
        $this->branch = $branch;
    }

    public function lastUpdate() : string
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(string $lastUpdate) : void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function url() : string
    {
        return $this->url;
    }

    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    public function pullRquestStatus() : string
    {
        return $this->pullRequestStatus;
    }

    public function setPullRequestStatus(string $pullRequestStatus) : void
    {
        $this->pullRequestStatus = $pullRequestStatus;
    }

    public function hasDirectory() : bool
    {
        return $this->hasDirectory;
    }

    public function setHasDirectory(bool $hasDirectory) : void
    {
        $this->hasDirectory = $hasDirectory;
    }

    public function directory() : string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory) : void
    {
        $this->directory = $directory;
    }

    public function pullRequestName() : string
    {
        return $this->pullRequestName;
    }

    public function setPullRequestName(string $pullRequestName) : void
    {
        $this->pullRequestName = $pullRequestName;
    }
}
