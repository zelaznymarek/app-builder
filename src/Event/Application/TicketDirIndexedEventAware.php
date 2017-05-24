<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface TicketDirIndexedEventAware
{
    public function onTicketDirIndexed(TicketDirIndexedEvent $event) : void;
}
