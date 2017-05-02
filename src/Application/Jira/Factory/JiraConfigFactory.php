<?php

declare(strict_types=1);

namespace Pvg\Application\Jira\Factory;

use JiraRestApi\Configuration\ArrayConfiguration;

class JiraConfigFactory
{
    /** @var ArrayConfiguration */
    public $applicationConfig;

    public function __construct(array $applicationConfig)
    {
        $this->applicationConfig = new ArrayConfiguration([
            'jiraHost'     => $applicationConfig['parameters']['jira.host'],
            'jiraUser'     => $applicationConfig['parameters']['jira.authentication.username'],
            'jiraPassword' => $applicationConfig['parameters']['jira.authentication.password'],
        ]);
    }

    public function applicationConfig() : ArrayConfiguration
    {
        return $this->applicationConfig;
    }
}
