<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager;

use Psr\Log\LoggerInterface;
use Pvg\Application\Module\TaskManager\Factory\TaskFactory;
use Pvg\Application\Module\TaskManager\Task\Task;
use Pvg\Event\Application\FullTicketBuiltEvent;
use Pvg\Event\Application\FullTicketBuiltEventAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TaskManagerService implements FullTicketBuiltEventAware
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var TaskFactory */
    private $taskFactory;

    public function __construct(
        TaskFactory $taskFactory,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $this->taskFactory = $taskFactory;
        $this->logger      = $logger;
        $this->dispatcher  = $dispatcher;
    }

    /**
     * Creates proper task for given ticket and passes it to process.
     */
    public function onFullTicketBuilt(FullTicketBuiltEvent $event) : void
    {
        $task = $this->taskFactory->create($event->ticket());
        $this->process($task);
    }

    /**
     * Processes passed task with ticket.
     */
    private function process(Task $task) : void
    {
        $task->execute();
    }
}
