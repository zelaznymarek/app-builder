<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Exception\MisguidedTaskException;
use AppBuilder\Application\Module\TaskManager\Task\NoActionTask;
use AppBuilder\Application\Module\TaskManager\Task\Task;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use FileNotFoundException;
use Psr\Log\LoggerInterface;

class TaskFactory
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Parameters */
    private $applicationParams;

    /** @var FileManagerService */
    private $fileManager;

    public function __construct(
        LoggerInterface $logger,
        Parameters $applicationParams,
        FileManagerService $fileManager
    ) {
        $this->logger            = $logger;
        $this->applicationParams = $applicationParams;
        $this->fileManager       = $fileManager;
    }

    public function create(Ticket $ticket) : Task
    {
        $factoriesByPriority = $this->groupByPriority($this->createFactories());

        return $this->matchTaskToTicket($ticket, $factoriesByPriority);
    }

    /**
     * Groups task factories by priority.
     */
    private function groupByPriority(array $factories) : array
    {
        $factoriesByPriority = [];
        foreach ($factories as $factory) {
            $factoriesByPriority[$factory::PRIORITY][] = $factory;
        }
        ksort($factoriesByPriority);

        return $factoriesByPriority;
    }

    /**
     * Return objects of all available task factories.
     */
    private function createFactories() : array
    {
        return [
            new CreateTicketDirTaskFactory(),
            new RemoveFinishedTicketTaskFactory(),
            new UpdateTicketTaskFactory(),
        ];
    }

    /**
     * Matches task to passed application.
     */
    private function matchTaskToTicket(Ticket $ticket, array $factoriesByPriority) : Task
    {
        foreach ($factoriesByPriority as $currentPriorityFactories) {
            foreach ($currentPriorityFactories as $factory) {
                try {
                    return $factory->create($ticket, $this->applicationParams, $this->fileManager);
                } catch (MisguidedTaskException $exception) {
                    continue;
                } catch (FileNotFoundException $exception) {
                    $this->logger->critical($exception->getMessage(), [$exception]);
                }
            }
        }
        $this->logger->info('No task matched, no action performed.');

        return new NoActionTask();
    }
}
