<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use Symfony\Component\Filesystem\Exception\IOException;

class RemoveDoneTicketTask implements Task
{
    /** @var Ticket */
    private $ticket;

    /** @var string */
    private $homeDir;

    /** @var FileManagerService */
    private $fileManager;

    public function __construct(Ticket $ticket, Parameters $applicationParams, FileManagerService $fileManager)
    {
        $this->ticket      = $ticket;
        $this->homeDir     = $applicationParams->projectsHomeDir();
        $this->fileManager = $fileManager;
    }

    /**
     * Removes application directory when its done.
     *
     * @throws IOException
     */
    public function execute() : bool
    {
        $this->fileManager->remove($this->homeDir . $this->ticket->key());

        return true;
    }
}
