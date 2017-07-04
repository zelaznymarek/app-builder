<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\Jira\ValueObject\JiraTicketStatus;
use AppBuilder\Application\Module\TaskManager\Exception\MisguidedTaskException;
use AppBuilder\Application\Module\TaskManager\Task\UpdateTicketTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use FileNotFoundException;

class UpdateTicketTaskFactory
{
    /** @var int */
    public const PRIORITY = 20;

    /** @var string */
    private const COMPARED_STATUS = JiraTicketStatus::WORK_FINISHED;

    /** @var Parameters */
    private $applicationParams;

    /** @var FileManagerService */
    private $fileManager;

    /**
     * If application is up to date throws MisguidedTaskException.
     * Otherwise returns UpdateTicketTask.
     *
     * @throws FileNotFoundException
     * @throws MisguidedTaskException
     */
    public function create(
        Ticket $ticket,
        Parameters $applicationParams,
        FileManagerService $fileManager
    ) : UpdateTicketTask {
        $this->applicationParams = $applicationParams;
        $this->fileManager       = $fileManager;
        if ($this->isUpToDate($ticket)) {
            throw new MisguidedTaskException(UpdateTicketTask::class);
        }

        return new UpdateTicketTask($ticket, $applicationParams, $this->fileManager);
    }

    /**
     * Checks wether ticket status is same to in-application status
     *
     * @throws FileNotFoundException
     */
    protected function isWorkFinished(Ticket $ticket, string $filepath) : bool
    {
        if (!$this->fileManager->fileExists($filepath)) {
            throw new FileNotFoundException('Could not open ' . $this->applicationParams->snapshotFileName());
        }
        $content = $this->fileManager->fileGetContent($filepath);

        $tempArray = explode(';', $content);

        $snapshot = [
            'status'    => $tempArray[0],
            'timestamp' => $tempArray[1],
        ];

        return $ticket->compareStatus($snapshot['status']);
    }

    /**
     * Checks whether application in directory is up to date.
     * Returns false if status changes to 'work finished'.
     *
     * @throws FileNotFoundException
     */
    private function isUpToDate(Ticket $ticket) : bool
    {
        if (!$ticket->compareStatus(static::COMPARED_STATUS)) {
            return true;
        }

        /** @var string */
        $filepath = $this->fileManager
            ->combineFilepath(
                $this->applicationParams->projectsHomeDir(),
                $ticket->key(),
                $this->applicationParams->snapshotFileName()
            );

        if ($ticket->hasDirectory()) {
            return !$this->isWorkFinished($ticket, $filepath);
        }

        return true;
    }
}
