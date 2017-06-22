<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager;

use AppBuilder\Application\Module\TaskManager\Factory\TaskFactory;
use AppBuilder\Application\Module\TaskManager\Task\Task;
use AppBuilder\Event\Application\FullTicketBuiltEvent;
use AppBuilder\Event\Application\FullTicketBuiltEventAware;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;

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
        try {
            $task->execute();
        } catch (IOException $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);
        }
    }
}
