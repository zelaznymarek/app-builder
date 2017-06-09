<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Factory;

use FileNotFoundException;
use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Application\Model\ValueObject\Ticket;
use Pvg\Application\Module\TaskManager\Exception\MisguidedTaskException;
use Pvg\Application\Module\TaskManager\Task\NoActionTask;
use Pvg\Application\Module\TaskManager\Task\Task;

class TaskFactory
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Parameters */
    private $applicationParams;

    public function __construct(
        LoggerInterface $logger,
        Parameters $applicationParams
    ) {
        $this->logger            = $logger;
        $this->applicationParams = $applicationParams;
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
            new RemoveDoneTicketTaskFactory(),
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
                    return $factory->create($ticket, $this->applicationParams);
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
