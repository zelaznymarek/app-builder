<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use Nette\DirectoryNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

class UpdateTicketTask implements Task
{
    /** @var string */
    private $path;

    /** @var Ticket */
    private $ticket;

    /** @var Parameters */
    private $applicationParams;

    /** @var FileManagerService */
    private $fileManager;

    public function __construct(Ticket $ticket, Parameters $applicationParams, FileManagerService $fileManager)
    {
        $this->applicationParams = $applicationParams;
        $this->ticket            = $ticket;
        $this->path              = $applicationParams->path($ticket->key());
        $this->fileManager       = $fileManager;
    }

    /**
     * Updates application code, if tickets status changes to 'work finished'.
     *
     * @throws DirectoryNotFoundException
     * @throws IOException
     */
    public function execute() : bool
    {
        return
            $this->executeGitPull($this->path)
            && $this->createSnapshot()
            && $this->executeComposerInstall($this->path);
    }

    /**
     * Runs git clone command in ticket directory. Returns true when process is finished.
     * Uses SSH key for authorization.
     *
     * @throws DirectoryNotFoundException
     */
    private function executeGitPull(string $path) : bool
    {
        $pullCommand = $this->combinePullCommand($this->ticket->branch());
        $this->fileManager->changeDir($path);
        exec($pullCommand);

        return true;
    }

    /**
     * Combines string with valid git pull command.
     */
    private function combinePullCommand(string $branch) : string
    {
        return 'git pull origin ' . $branch;
    }

    /**
     * Creates file with tickets current status and timestamp inside ticket dir.
     *
     * @throws IOException
     */
    private function createSnapshot() : bool
    {
        $snapshotPath = $this->applicationParams->snapshotPath($this->ticket->key());
        $snap         = $this->ticket->ticketStatus() . ';' . date('Y-m-d h:m:s');
        $this->fileManager->filePutContent($snapshotPath, $snap);

        return true;
    }

    /**
     * Runs composer install command in ticket directory.
     * Returns true when process is finished.
     */
    private function executeComposerInstall(string $path) : bool
    {
        exec('composer install --working-dir=' . $path);

        return true;
    }
}
