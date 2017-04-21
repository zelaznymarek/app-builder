<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

interface ApplicationInitializedEventAwareInterface
{
    public function onApplicationInitialized(ApplicationInitializedEvent $event) : void;
}
