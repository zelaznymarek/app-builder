<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Task;

class NoActionTask implements Task
{
    /**
     * Performs no action for application.
     */
    public function execute() : void
    {
    }
}
