<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira\Factory;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\ExternalLibraryJiraService;
use Pvg\Application\Module\Jira\QueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JiraServiceFactory
{
    public function create(
        array $applicationConfig,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        QueryRepository $queryRepository
    ) : ExternalLibraryJiraService {
        return new ExternalLibraryJiraService(
            new IssueService(
                new ArrayConfiguration([
                    'jiraHost'     => $applicationConfig['parameters']['jira.host'],
                    'jiraUser'     => $applicationConfig['parameters']['jira.authentication.username'],
                    'jiraPassword' => $applicationConfig['parameters']['jira.authentication.password'],
                ])
            ),
            $logger,
            $dispatcher,
            $queryRepository
        );
    }
}
