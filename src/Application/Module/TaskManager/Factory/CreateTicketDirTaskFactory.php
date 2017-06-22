<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Exception\MisguidedTaskException;
use AppBuilder\Application\Module\TaskManager\Task\CreateTicketDirTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;

class CreateTicketDirTaskFactory
{
    /** @var int */
    public const PRIORITY = 10;

    /**
     * If application has no directory, returns CreateTicketDirTask.
     * Throws MisguidedTaskException if directory exists or application status is done.
     *
     * @throws MisguidedTaskException
     */
    public function create(
        Ticket $ticket,
        Parameters $applicationParams,
        FileManagerService $fileManager
    ) : CreateTicketDirTask {
        if ($ticket->hasDirectory()
            || $ticket->isDone()
            || !$ticket->hasBranch()
        ) {
            throw new MisguidedTaskException(CreateTicketDirTask::class);
        }

        return new CreateTicketDirTask($ticket, $applicationParams, $fileManager);
    }
}
