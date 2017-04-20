<?php

namespace PVG\Event\Application;

interface ApplicationInitializedEventAwareInterface
{
    public function onApplicationInitialized(ApplicationInitializedEvent $event);
}
