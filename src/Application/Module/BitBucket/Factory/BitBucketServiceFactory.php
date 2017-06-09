<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\BitBucket\Factory;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Module\BitBucket\ExternalLibraryBitBucketService;
use Pvg\Application\Module\HttpClient\ExternalLibraryHttpClient;
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
