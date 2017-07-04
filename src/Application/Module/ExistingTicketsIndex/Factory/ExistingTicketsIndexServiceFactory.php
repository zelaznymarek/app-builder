<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\ExistingTicketsIndex\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\ExistingTicketsIndex\ExistingTicketsIndexService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExistingTicketsIndexServiceFactory
{
    public function create(
        Parameters $applicationParams,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) : ExistingTicketsIndexService {
        return new ExistingTicketsIndexService($applicationParams, $dispatcher, $logger);
    }
}
