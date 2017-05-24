<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

class FullTicket implements TicketBuilder
{
    /** @var Ticket */
    private $ticket;

    public function __construct()
    {
        $this->ticket = new Ticket();
    }

    public function addId(int $id) : void
    {
        $this->ticket->setId($id);
    }

    public function addKey(string $key) : void
    {
        $this->ticket->setKey($key);
    }

    public function addAssigneeName(string $name) : void
    {
        $this->ticket->setAssigneeName($name);
    }

    public function addAssigneeDisplayName(string $displayName) : void
    {
        $this->ticket->setAssigneeDisplayName($displayName);
    }

    public function addAssigneeEmail(string $email) : void
    {
        $this->ticket->setAssigneeEmail($email);
    }

    public function addIsAssigneeActive(bool $isActive) : void
    {
        $this->ticket->setIsAssigneeActive($isActive);
    }

    public function addTicketStatus(string $ticketStatus) : void
    {
        $this->ticket->setTicketStatus($ticketStatus);
    }

    public function addTicketStatusCategory(string $ticketStatusCategory) : void
    {
        $this->ticket->setTicketStatusCategory($ticketStatusCategory);
    }

    public function addComponents(string $components) : void
    {
        $this->ticket->setComponents($components);
    }

    public function addType(string $type) : void
    {
        $this->ticket->setType($type);
    }

    public function addProject(string $project) : void
    {
        $this->ticket->setProject($project);
    }

    public function addFixVersion(string $fixVersion) : void
    {
        $this->ticket->setFixVersion($fixVersion);
    }

    public function addSummary(string $summary) : void
    {
        $this->ticket->setSummary($summary);
    }

    public function addBranch(string $branch) : void
    {
        $this->ticket->setBranch($branch);
    }

    public function addLastUpdate(string $lastUpdate) : void
    {
        $this->ticket->setLastUpdate($lastUpdate);
    }

    public function addUrl(string $url) : void
    {
        $this->ticket->setUrl($url);
    }

    public function addPullRequestStatus(string $PrStatus) : void
    {
        $this->ticket->setPullRequestStatus($PrStatus);
    }

    public function addHasDirectory(bool $hasDir) : void
    {
        $this->ticket->setHasDirectory($hasDir);
    }

    public function addDirectory(string $dir) : void
    {
        $this->ticket->setDirectory($dir);
    }

    public function ticket() : Ticket
    {
        return $this->ticket;
    }

    public function addPullRequestName(string $PrName) : void
    {
        $this->ticket->setPullRequestName($PrName);
    }
}
