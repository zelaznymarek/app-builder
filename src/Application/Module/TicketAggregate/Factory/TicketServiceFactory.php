<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TicketAggregate\Factory;

use AppBuilder\Application\Module\TicketAggregate\TicketBuilder;
use AppBuilder\Application\Module\TicketAggregate\TicketService;
use Psr\Log\LoggerInterface;
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
