<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface TicketsFetchedEventAware
{
    public function onTicketsFetched(TicketsFetchedEvent $event) : void;
}
