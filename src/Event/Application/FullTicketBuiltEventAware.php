<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

interface FullTicketBuiltEventAware
{
    public function onFullTicketBuilt(FullTicketBuiltEvent $event) : void;
}
