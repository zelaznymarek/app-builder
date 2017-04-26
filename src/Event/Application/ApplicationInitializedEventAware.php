<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface ApplicationInitializedEventAware
{
    public function onApplicationInitialized(ApplicationInitializedEvent $event) : void;
}
