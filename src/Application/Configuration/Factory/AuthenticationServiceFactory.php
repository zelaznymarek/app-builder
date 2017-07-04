<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Configuration\Factory;

use AppBuilder\Application\Configuration\AuthenticationService;
use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Module\Jira\QueryRepository;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthenticationServiceFactory
{
    public static function create(
        Parameters $applicationParams,
        QueryRepository $queryRepository,
        FileManagerService $fileManager,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) : AuthenticationService {
        return new AuthenticationService(
            $queryRepository,
            new ExternalLibraryHttpClient(
                new Client(['base_uri' => $applicationParams->jiraHost()]),
                $applicationParams
            ),
            $fileManager,
            $logger,
            $dispatcher
        );
    }
}
