<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

interface CredentialsValidatedEventAware
{
    public function onCredentialsValidated(CredentialsValidatedEvent $event = null) : void;
}
