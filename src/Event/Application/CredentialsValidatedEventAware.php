<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface CredentialsValidatedEventAware
{
    public function onCredentialsValidated(CredentialsValidatedEvent $event) : void;
}
