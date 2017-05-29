<?php


namespace Pvg\Application\Module\TaskManager;

use Pvg\Application\Model\ValueObject\Ticket;

interface Task
{
    public function execute(Ticket $ticket) : void;
}
