<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

interface JiraService
{
    /**
     * Method uses provided credentials to connect JIRA.
     */
    public function login() : bool;
}
