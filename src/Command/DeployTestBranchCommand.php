<?php

namespace PVG\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployTestBranchCommand extends Command
{
    const NAME = 'app:watch';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Observe JIRA tickets and create test deploys if required');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load background service
    }

}
