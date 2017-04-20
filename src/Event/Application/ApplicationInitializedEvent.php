<?php

namespace PVG\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class ApplicationInitializedEvent extends Event
{
    const NAME = 'application.initialized';
}
