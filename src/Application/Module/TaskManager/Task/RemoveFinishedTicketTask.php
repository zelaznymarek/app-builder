<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use Symfony\Component\Filesystem\Exception\IOException;

class RemoveFinishedTicketTask implements Task
{
    /** @var Ticket */
    private $ticket;

    /** @var string */
    private $projectHomeDir;

    /** @var string */
    private $publicHostDir;

    /** @var FileManagerService */
    private $fileManager;

    public function __construct(Ticket $ticket, Parameters $applicationParams, FileManagerService $fileManager)
    {
        $this->ticket             = $ticket;
        $this->projectHomeDir     = $applicationParams->projectsHomeDir();
        $this->publicHostDir      = $applicationParams->symlinkTarget($ticket->key());
        $this->fileManager        = $fileManager;
    }

    /**
     * Removes application and symlinks directories when its finished.
     *
     * @throws IOException
     */
    public function execute() : bool
    {
        $this->fileManager->remove($this->projectHomeDir . $this->ticket->key());
        $this->fileManager->remove($this->publicHostDir);

        return true;
    }
}
