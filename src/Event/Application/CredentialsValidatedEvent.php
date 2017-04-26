<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class CredentialsValidatedEvent extends Event
{
    /** @var string */
    const NAME = 'credentials.validated';
}
