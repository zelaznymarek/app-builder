<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

interface TicketBuilder
{
    public function addId(int $id) : void;

    public function addKey(string $key) : void;

    public function addAssigneeName(string $name) : void;

    public function addAssigneeDisplayName(string $displayName) : void;

    public function addAssigneeEmail(string $email) : void;

    public function addIsAssigneeActive(bool $isActive) : void;

    public function addTicketStatus(string $ticketStatus) : void;

    public function addTicketStatusCategory(string $ticketStatusCategory) : void;

    public function addComponents(string $components) : void;

    public function addType(string $type) : void;

    public function addProject(string $project) : void;

    public function addFixVersion(string $fixVersion) : void;

    public function addSummary(string $summary) : void;

    public function addBranch(string $branch) : void;

    public function addLastUpdate(string $lastUpdate) : void;

    public function addUrl(string $url) : void;

    public function addPullRequestStatus(string $PrStatus) : void;

    public function addPullRequestName(string $PrName) : void;

    public function addHasDirectory(bool $hasDir) : void;

    public function addDirectory(string $dir) : void;

    public function ticket() : Ticket;
}
