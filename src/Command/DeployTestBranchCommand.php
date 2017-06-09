<?php

declare(strict_types = 1);

namespace Pvg\Command;

use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeployTestBranchCommand extends Command
{
    public const NAME = 'app:watch';

    /** @var LoggerInterface */
    private $logger;

    /** @var Parameters */
    private $applicationParams;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @throws LogicException
     */
    public function __construct(
        Parameters $applicationParams,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct(null);
        $this->applicationParams = $applicationParams;
        $this->logger            = $logger;
        $this->dispatcher        = $dispatcher;
    }

    protected function configure() : void
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Observe JIRA tickets and create test deploys if required');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->logger->info('Initializing application...');
        $this->dispatcher->dispatch(
            ApplicationInitializedEvent::NAME,
            new ApplicationInitializedEvent()
        );
    }
}
