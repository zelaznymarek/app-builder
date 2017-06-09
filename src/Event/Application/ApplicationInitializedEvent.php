<?php

declare(strict_types = 1);

namespace Pvg\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class ApplicationInitializedEvent extends Event
{
    public const NAME = 'application.initialized';
}
