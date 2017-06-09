<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Factory;

use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;
use Pvg\Application\Module\TaskManager\Exception\MisguidedTaskException;
use Pvg\Application\Module\TaskManager\Task\RemoveDoneTicketTask;

class RemoveDoneTicketTaskFactory
{
    /** @var int */
    public const PRIORITY = 30;

    /**
     * If application has 'Done' status, returns RemoveDoneTicketTask.
     * Otherwise throws MisguidedTaskException.
     *
     * @throws MisguidedTaskException
     */
    public function create(Ticket $ticket, Parameters $applicationParams) : RemoveDoneTicketTask
    {
        if (!$this->existsAndIsDone($ticket)) {
            throw new MisguidedTaskException(RemoveDoneTicketTask::class);
        }

        return new RemoveDoneTicketTask($ticket, $applicationParams);
    }

    /**
     * Returns true if application has directory and its status is Done.
     */
    private function existsAndIsDone(Ticket $ticket) : bool
    {
        return $ticket->hasDirectory() && $ticket->isDone();
    }
}
