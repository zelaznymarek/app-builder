<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use FileNotFoundException;
use Nette\DirectoryNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

class CreateTicketDirTask implements Task
{
    /** @var Ticket */
    private $ticket;

    /** @var Parameters */
    private $applicationParams;

    /** @var string */
    private $path;

    /** @var FileManagerService */
    private $fileManager;

    public function __construct(Ticket $ticket, Parameters $applicationParams, FileManagerService $fileManager)
    {
        $this->ticket            = $ticket;
        $this->applicationParams = $applicationParams;
        $this->path              = $applicationParams->path($ticket->key());
        $this->fileManager       = $fileManager;
    }

    /**
     * Creates directory for application, deploys it and installs symlinks.
     *
     * @throws FileNotFoundException
     * @throws DirectoryNotFoundException
     * @throws IOException
     */
    public function execute() : bool
    {
        return $this->createDirectory()
            && $this->executeGitClone()
            && $this->createSnapshot()
            && $this->executeComposerInstall()
            && $this->installSymlinks();
    }

    /**
     * Creates directory for application
     *
     * @throws FileNotFoundException
     */
    private function createDirectory() : bool
    {
        if (!$this->fileManager->createDir($this->path)) {
            throw new FileNotFoundException(
                'Could not create directory for ' . $this->ticket->key()
            );
        }

        return true;
    }

    /**
     * Runs git clone command in application directory. Returns true when process is finished.
     * Uses SSH key for authorization.
     *
     * @throws DirectoryNotFoundException
     */
    private function executeGitClone() : bool
    {
        $this->fileManager->changeDir($this->path);
        exec($this->combineCloneCommand());

        return true;
    }

    /**
     * Returns git clone bash command combined for particular application.
     */
    private function combineCloneCommand() : string
    {
        return 'git clone '
            . $this->applicationParams->bitbucketRepositoryHost()
            . $this->ticket->repository()
            . '.git -b '
            . $this->ticket->branch()
            . ' '
            . $this->path;
    }

    /**
     * Creates file with tickets current status and timestamp inside ticket dir.
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
    private function executeComposerInstall() : bool
    {
        exec('composer install --working-dir=' . $this->path);

        return true;
    }

    /**
     * Installs symlinks for application.
     *
     * @throws IOException
     */
    private function installSymlinks() : bool
    {
        $ticketKey = $this->ticket->key();

        $this->fileManager->createSymlink(
            $this->applicationParams->symlinkTarget($ticketKey),
            $this->applicationParams->symlinkSource($ticketKey)
        );

        return true;
    }
}
