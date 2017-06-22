<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

interface JiraTicketMappedEventAware
{
    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void;
}
