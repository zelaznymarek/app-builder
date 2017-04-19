<?php

namespace PVG\Event;

use Symfony\Component\EventDispatcher\Event;

class ApplicationInitializedEvent extends Event
{
    const NAME = 'application.initialized';
}
