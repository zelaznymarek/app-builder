<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\BitBucket\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\BitBucket\ExternalLibraryBitBucketService;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BitBucketServiceFactory
{
    public function create(
        Parameters $applicationParams,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) : ExternalLibraryBitBucketService {
        return new ExternalLibraryBitBucketService(
            new ExternalLibraryHttpClient(
                new Client(['base_uri' => $applicationParams->jiraHost()]),
                $applicationParams
            ),
            $logger,
            $dispatcher
        );
    }
}
