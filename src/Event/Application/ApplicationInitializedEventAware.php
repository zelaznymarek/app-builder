<?php

declare(strict_types = 1);

namespace AppBuilder\Event\Application;

interface ApplicationInitializedEventAware
{
    public function onApplicationInitialized(ApplicationInitializedEvent $event = null) : void;
}
