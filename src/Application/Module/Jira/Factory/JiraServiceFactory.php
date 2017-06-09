<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\Jira\Factory;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Module\HttpClient\ExternalLibraryHttpClient;
use Pvg\Application\Module\Jira\ExternalLibraryJiraService;
use Pvg\Application\Module\Jira\QueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JiraServiceFactory
{
    public function create(
        Parameters $applicationParams,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        QueryRepository $queryRepository
    ) : ExternalLibraryJiraService {
        return new ExternalLibraryJiraService(
            new ExternalLibraryHttpClient(
                new Client(['base_uri' => $applicationParams->jiraHost()]),
                    $applicationParams
            ),
            $logger,
            $dispatcher,
            $queryRepository
        );
    }
}
