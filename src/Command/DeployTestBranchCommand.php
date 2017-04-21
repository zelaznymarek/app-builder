<?php

declare(strict_types=1);

namespace Pvg\Command;

use Psr\Log\LoggerInterface;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeployTestBranchCommand extends Command
{
    const NAME = 'app:watch';

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $configArray;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @throws LogicException
     */
    public function __construct(array $configArray, LoggerInterface $logger, EventDispatcherInterface $dispatcher)
    {
        parent::__construct(null);
        $this->configArray = $configArray;
        $this->logger      = $logger;
        $this->dispatcher  = $dispatcher;
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
