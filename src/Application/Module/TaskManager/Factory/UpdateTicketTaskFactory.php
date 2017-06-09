<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Factory;

use FileNotFoundException;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;
use Pvg\Application\Module\Jira\ValueObject\JiraTicketStatus;
use Pvg\Application\Module\TaskManager\Exception\MisguidedTaskException;
use Pvg\Application\Module\TaskManager\Task\UpdateTicketTask;

class UpdateTicketTaskFactory
{
    /** @var int */
    public const PRIORITY = 20;

    /** @var string */
    private const COMPARED_STATUS = JiraTicketStatus::WORK_FINISHED;

    /**
     * If application is up to date throws MisguidedTaskException.
     * Otherwise returns UpdateTicketTask.
     *
     * @throws FileNotFoundException
     * @throws MisguidedTaskException
     */
    public function create(Ticket $ticket, Parameters $applicationParams) : UpdateTicketTask
    {
        if ($this->isUpToDate($ticket, $applicationParams)) {
            throw new MisguidedTaskException(UpdateTicketTask::class);
        }

        return new UpdateTicketTask($ticket, $applicationParams);
    }

    /**
     * Checks whether application in directory is up to date.
     * Returns false if status changes to 'work finished'.
     *
     * @throws FileNotFoundException
     */
    private function isUpToDate(Ticket $ticket, Parameters $applicationParams) : bool
    {
        if (!$ticket->compareStatus(static::COMPARED_STATUS)) {
            return true;
        }

        $filepath = $applicationParams->projectsHomeDir()
            . $ticket->key()
            . $applicationParams->snapshotFileName();

        if ($ticket->hasDirectory()) {
            if (!file_exists($filepath) || !is_readable($filepath)) {
                throw new FileNotFoundException('Could not open ' . $applicationParams->snapshotFileName());
            }
            $content = file_get_contents($filepath);

            $tempArray = explode(';', $content);

            $snapshot = [
                'status'    => $tempArray[0],
                'timestamp' => $tempArray[1],
            ];
            if (!$ticket->compareStatus($snapshot['status'])) {
                return false;
            }
        }

        return true;
    }
}
