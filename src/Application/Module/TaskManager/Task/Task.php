<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Task;

interface Task
{
    /**
     * Executes task for particular application.
     */
    public function execute() : void;
}
