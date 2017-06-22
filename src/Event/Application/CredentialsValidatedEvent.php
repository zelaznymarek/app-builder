<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class CredentialsValidatedEvent extends Event
{
    public const NAME = 'credentials.validated';
}
