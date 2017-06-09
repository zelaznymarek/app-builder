<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Task;

use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;

class RemoveDoneTicketTask implements Task
{
    /** @var Ticket */
    private $ticket;

    /** @var string */
    private $homeDir;

    public function __construct(Ticket $ticket, Parameters $applicationParams)
    {
        $this->ticket  = $ticket;
        $this->homeDir = $applicationParams->projectsHomeDir();
    }

    /**
     * Removes application directory when its done.
     */
    public function execute() : void
    {
        rmdir($this->homeDir . $this->ticket->key());
    }
}
