<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TicketAggregate\Factory;

use Psr\Log\LoggerInterface;
use Pvg\Application\Module\TicketAggregate\TicketBuilder;
use Pvg\Application\Module\TicketAggregate\TicketService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TicketServiceFactory
{
    public function create(
        TicketBuilder $builder,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) : TicketService {
        return new TicketService($builder, $dispatcher, $logger);
    }
}
