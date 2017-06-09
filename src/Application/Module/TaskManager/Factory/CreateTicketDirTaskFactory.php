<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Factory;

use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;
use Pvg\Application\Module\TaskManager\Exception\MisguidedTaskException;
use Pvg\Application\Module\TaskManager\Task\CreateTicketDirTask;

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
    public function create(Ticket $ticket, Parameters $applicationParams) : CreateTicketDirTask
    {
        if ($ticket->hasDirectory()
            || $ticket->isDone()
            || !$ticket->hasBranch()
        ) {
            throw new MisguidedTaskException(CreateTicketDirTask::class);
        }

        return new CreateTicketDirTask($ticket, $applicationParams);
    }
}
