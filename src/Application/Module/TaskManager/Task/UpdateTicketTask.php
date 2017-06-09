<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Task;

use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;

class UpdateTicketTask implements Task
{
    /** @var string */
    private $path;

    /** @var Ticket */
    private $ticket;

    /** @var Parameters */
    private $applicationParams;

    public function __construct(Ticket $ticket, Parameters $applicationParams)
    {
        $this->applicationParams = $applicationParams;
        $this->ticket            = $ticket;
        $this->path              = $applicationParams->path($ticket->key());
    }

    /**
     * Updates application code, if tickets status changes to 'work finished'.
     */
    public function execute() : void
    {
        $this->executeGitPull($this->path);
        $this->createSnapshot();
        $this->executeComposerInstall($this->path);
    }

    /**
     * Runs git clone command in ticket directory. Returns true when process is finished.
     * Uses SSH key for authorization.
     */
    private function executeGitPull(string $path) : bool
    {
        $pullCommand = $this->combinePullCommand($this->ticket->branch());
        chdir($path);
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
     * Runs composer install command in ticket directory.
     * Returns true when process is finished.
     */
    private function executeComposerInstall(string $path) : bool
    {
        exec('composer install --working-dir=' . $path);

        return true;
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
}
