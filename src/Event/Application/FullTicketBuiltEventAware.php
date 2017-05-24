<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface FullTicketBuiltEventAware
{
    public function onFullTicketBuilt(FullTicketBuiltEvent $event) : void;
}
