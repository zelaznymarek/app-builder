<?php

declare(strict_types=1);

namespace Pvg\Application\Module\ExistingTicketsIndex\Factory;

use Psr\Log\LoggerInterface;
use Pvg\Application\Module\ExistingTicketsIndex\ExistingTicketsIndexService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExistingTicketsIndexServiceFactory
{
    public function create(
        array $configArray,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) : ExistingTicketsIndexService {
        return new ExistingTicketsIndexService($configArray, $dispatcher, $logger);
    }
}
