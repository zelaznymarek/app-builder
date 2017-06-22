<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Exception\MisguidedTaskException;
use AppBuilder\Application\Module\TaskManager\Task\RemoveFinishedTicketTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;

class RemoveFinishedTicketTaskFactory
{
    /** @var int */
    public const PRIORITY = 30;

    /**
     * If application has 'Done' status, returns RemoveFinishedTicketTask.
     * Otherwise throws MisguidedTaskException.
     *
     * @throws MisguidedTaskException
     */
    public function create(
        Ticket $ticket,
        Parameters $applicationParams,
        FileManagerService $fileManager
    ) : RemoveFinishedTicketTask {
        if (!$this->existsAndIsDone($ticket)) {
            throw new MisguidedTaskException(RemoveFinishedTicketTask::class);
        }

        return new RemoveFinishedTicketTask($ticket, $applicationParams, $fileManager);
    }

    /**
     * Returns true if application has directory and its status is Done.
     */
    private function existsAndIsDone(Ticket $ticket) : bool
    {
        return $ticket->hasDirectory() && $ticket->isDone();
    }
}
