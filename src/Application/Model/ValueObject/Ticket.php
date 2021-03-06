<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Model\ValueObject;

use AppBuilder\Application\Model\Exception\NullArgumentException;
use AppBuilder\Application\Module\Jira\ValueObject\JiraTicketStatus;

/**
 * Class represents complete ticket.
 */
class Ticket
{
    /** @var string */
    private const DONE_STATUS = 'Done';

    /** @var ?int */
    private $id;

    /** @var ?string */
    private $key;

    /** @var ?string */
    private $assigneeName;

    /** @var ?string */
    private $assigneeDisplayName;

    /** @var ?string */
    private $assigneeEmail;

    /** @var ?bool */
    private $isAssigneeActive;

    /** @var ?string */
    private $ticketStatus;

    /** @var bool */
    private $isDone;

    /** @var ?string */
    private $ticketStatusCategory;

    /** @var ?string */
    private $components;

    /** @var ?string */
    private $type;

    /** @var ?string */
    private $project;

    /** @var ?string */
    private $fixVersion;

    /** @var ?string */
    private $summary;

    /** @var ?string */
    private $branch;

    /** @var bool */
    private $hasBranch;

    /** @var ?string */
    private $lastUpdate;

    /** @var ?string */
    private $url;

    /** @var ?string */
    private $pullRequestStatus;

    /** @var ?string */
    private $pullRequestName;

    /** @var ?string */
    private $repository;

    /** @var ?bool */
    private $hasDirectory;

    /** @var ?string */
    private $directory;

    public function __construct(
        array $ticketData,
        array $prData,
        array $dirData
    ) {
        $this->setId($ticketData['id']);
        $this->setKey($ticketData['ticket_key']);
        $this->setAssigneeName($ticketData['assignee_name']);
        $this->setAssigneeDisplayName($ticketData['assignee_display_name']);
        $this->setAssigneeEmail($ticketData['assignee_email']);
        $this->setIsAssigneeActive($ticketData['assignee_active']);
        $this->setTicketStatus($ticketData['status']);
        $this->setIsDone($ticketData['status']);
        $this->setTicketStatusCategory($ticketData['status_category']);
        $this->setComponents($ticketData['components']);
        $this->setType($ticketData['ticket_type']);
        $this->setProject($ticketData['project']);
        $this->setFixVersion($ticketData['fix_version']);
        $this->setSummary($ticketData['summary']);

        $this->setBranch($prData['pull_request_branch']);
        $this->setHasBranch($prData['pull_request_branch']);
        $this->setLastUpdate($prData['pull_request_last_update']);
        $this->setUrl($prData['pull_request_url']);
        $this->setPullRequestStatus($prData['pull_request_status']);
        $this->setPullRequestName($prData['pull_request_name']);
        $this->setRepository($prData['repository']);

        $this->setHasDirectory($dirData['ticketExists']);
        $this->setDirectory($dirData['ticketDir']);
    }

    /**
     * Compares passed status to tickets status.
     * Returns true if same, false if not.
     */
    public function compareStatus(string $status) : bool
    {
        return mb_strtolower(JiraTicketStatus::createFromString($status)->status())
            === mb_strtolower($this->ticketStatus);
    }

    public function id() : int
    {
        return $this->id;
    }

    public function key() : string
    {
        return $this->key;
    }

    public function assigneeName() : ?string
    {
        return $this->assigneeName;
    }

    public function assigneeDisplayName() : ?string
    {
        return $this->assigneeDisplayName;
    }

    public function assigneeEmail() : ?string
    {
        return $this->assigneeEmail;
    }

    public function isAssigneeActive() : ?bool
    {
        return $this->isAssigneeActive;
    }

    public function ticketStatus() : string
    {
        return $this->ticketStatus;
    }

    public function ticketStatusCategory() : ?string
    {
        return $this->ticketStatusCategory;
    }

    public function components() : ?string
    {
        return $this->components;
    }

    public function type() : ?string
    {
        return $this->type;
    }

    public function project() : string
    {
        return $this->project;
    }

    public function fixVersion() : ?string
    {
        return $this->fixVersion;
    }

    public function summary() : ?string
    {
        return $this->summary;
    }

    public function branch() : ?string
    {
        return $this->branch;
    }

    public function lastUpdate() : string
    {
        return $this->lastUpdate;
    }

    public function url() : ?string
    {
        return $this->url;
    }

    public function pullRquestStatus() : ?string
    {
        return $this->pullRequestStatus;
    }

    public function repository() : ?string
    {
        return $this->repository;
    }

    public function hasDirectory() : bool
    {
        return $this->hasDirectory;
    }

    public function directory() : ?string
    {
        return $this->directory;
    }

    public function pullRequestName() : ?string
    {
        return $this->pullRequestName;
    }

    public function isDone() : bool
    {
        return $this->isDone;
    }

    public function hasBranch() : bool
    {
        return $this->hasBranch;
    }

    public function setRepository(?string $repository) : void
    {
        $this->repository = $repository;
    }

    public function setIsDone(string $status) : void
    {
        $this->isDone = false;
        if (static::DONE_STATUS === $status) {
            $this->isDone = true;
        }
    }

    public function setHasBranch(?string $branch) : void
    {
        $this->hasBranch = true;
        if (null === $branch) {
            $this->hasBranch = false;
        }
    }

    /**
     * @throws NullArgumentException
     */
    private function setId(?int $id) : void
    {
        if (null === $id) {
            throw new NullArgumentException('Ticket ID property cannot be null');
        }
        $this->id = $id;
    }

    /**
     * @throws NullArgumentException
     */
    private function setKey(?string $key) : void
    {
        if (null === $key) {
            throw new NullArgumentException('Ticket key cannot be null');
        }
        $this->key = $key;
    }

    private function setAssigneeName(?string $assigneeName) : void
    {
        $this->assigneeName = $assigneeName;
    }

    private function setAssigneeDisplayName(?string $assigneeDisplayName) : void
    {
        $this->assigneeDisplayName = $assigneeDisplayName;
    }

    private function setAssigneeEmail(?string $assigneeEmail) : void
    {
        $this->assigneeEmail = $assigneeEmail;
    }

    private function setIsAssigneeActive(?bool $isAssigneeActive) : void
    {
        $this->isAssigneeActive = $isAssigneeActive;
    }

    /**
     * @throws NullArgumentException
     */
    private function setTicketStatus(?string $ticketStatus) : void
    {
        if (null === $ticketStatus) {
            throw new NullArgumentException('Ticket status cannot be null');
        }
        $this->ticketStatus = JiraTicketStatus::createFromString($ticketStatus)->status();
    }

    private function setTicketStatusCategory(?string $ticketStatusCategory) : void
    {
        $this->ticketStatusCategory = $ticketStatusCategory;
    }

    private function setComponents(?string $components) : void
    {
        $this->components = $components;
    }

    private function setType(?string $type) : void
    {
        $this->type = $type;
    }

    /**
     * @throws NullArgumentException
     */
    private function setProject(?string $project) : void
    {
        if (null === $project) {
            throw new NullArgumentException('Ticket project cannot be null');
        }
        $this->project = $project;
    }

    private function setFixVersion(?string $fixVersion) : void
    {
        $this->fixVersion = $fixVersion;
    }

    private function setSummary(?string $summary) : void
    {
        $this->summary = $summary;
    }

    private function setBranch(?string $branch) : void
    {
        $this->branch = $branch;
    }

    private function setLastUpdate(?string $lastUpdate) : void
    {
        $this->lastUpdate = $lastUpdate;
    }

    private function setUrl(?string $url) : void
    {
        $this->url = $url;
    }

    private function setPullRequestStatus(?string $pullRequestStatus) : void
    {
        $this->pullRequestStatus = $pullRequestStatus;
    }

    private function setHasDirectory(?bool $hasDirectory) : void
    {
        if (null === $hasDirectory) {
            throw new NullArgumentException('Ticket "hasDirectory" cannot be null');
        }
        $this->hasDirectory = $hasDirectory;
    }

    private function setDirectory(?string $directory) : void
    {
        $this->directory = $directory;
    }

    private function setPullRequestName(?string $pullRequestName) : void
    {
        $this->pullRequestName = $pullRequestName;
    }
}
