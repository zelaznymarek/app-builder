<?php

declare(strict_types=1);

namespace Pvg\Application\Module\BitBucket\Factory;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\BitBucket\ExternalLibraryBitBucketService;
use Pvg\Application\Module\BitBucket\GuzzleClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BitBucketServiceFactory
{
    public function create(
        array $applicationConfig,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) : ExternalLibraryBitBucketService {
        return new ExternalLibraryBitBucketService(
            new GuzzleClient(
                new Client(['base_uri' => $applicationConfig['parameters']['jira.host']]),
                [
                    'user'     => $applicationConfig['parameters']['jira.authentication.username'],
                    'password' => $applicationConfig['parameters']['jira.authentication.password'],
                ]),
            $logger,
            $dispatcher
        );
    }
}
