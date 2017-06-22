<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\Jira\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Module\Jira\ExternalLibraryJiraService;
use AppBuilder\Application\Module\Jira\QueryRepository;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
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
