<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira\Factory;

use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\TicketCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FilesystemAdapterFactory
{
    public static function create(LoggerInterface $logger) : TicketCache
    {
        return new TicketCache($logger, new FilesystemAdapter());
    }
}
