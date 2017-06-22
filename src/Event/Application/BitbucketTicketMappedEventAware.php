<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

interface BitbucketTicketMappedEventAware
{
    public function onBitbucketTicketMapped(BitbucketTicketMappedEvent $event) : void;
}
