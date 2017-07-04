<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Task;

interface Task
{
    /**
     * Executes task for particular application.
     */
    public function execute() : bool;
}
