<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface CredentialsRejectedEventAware
{
    public function onCredentialsRejected(CredentialsRejectedEvent $event) : void;
}
