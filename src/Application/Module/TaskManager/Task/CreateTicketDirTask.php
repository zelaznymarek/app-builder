<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Task;

use FileNotFoundException;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;

class CreateTicketDirTask implements Task
{
    /** @var Ticket */
    private $ticket;

    /** @var Parameters */
    private $applicationParams;

    /** @var string */
    private $path;

    public function __construct(Ticket $ticket, Parameters $applicationParams)
    {
        $this->ticket            = $ticket;
        $this->applicationParams = $applicationParams;
        $this->path              = $applicationParams->path($ticket->key());
    }

    /**
     * Creates directory for application, deploys it and installs symlinks.
     *
     * @throws FileNotFoundException
     */
    public function execute() : void
    {
        $this->createDirectory();
        $this->executeGitClone();
        $this->createSnapshot();
        $this->executeComposerInstall();
        $this->installSymlinks();
    }

    /**
     * Creates directory for application
     *
     * @throws FileNotFoundException
     */
    private function createDirectory() : void
    {
        if (!mkdir($this->path) && !is_dir($this->applicationParams->projectsHomeDir())) {
            throw new FileNotFoundException(
                'Could not create directory for '
                . $this->ticket->key()
            );
        }
    }

    /**
     * Runs git clone command in application directory. Returns true when process is finished.
     * Uses SSH key for authorization.
     */
    private function executeGitClone() : bool
    {
        chdir($this->path);
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
    private function createSnapshot() : void
    {
        $snapshotPath = $this->applicationParams->snapshotPath($this->ticket->key());
        $snap         = $this->ticket->ticketStatus() . ';' . date('Y-m-d h:m:s');
        file_put_contents($snapshotPath, $snap);
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
     */
    private function installSymlinks() : bool
    {
        $ticketKey = $this->ticket->key();

        return symlink(
            $this->applicationParams->symlinkTarget($ticketKey),
            $this->applicationParams->symlinkSource($ticketKey)
        );
    }
}
