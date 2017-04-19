#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use PVG\Command\DeployTestBranchCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

$application = new Application('Test branch builder', '1.0.0');
$container   = new ContainerBuilder();
$fileLocator = new FileLocator(__DIR__);

try {
    $container->set('app.file_locator', $fileLocator);
    $container->set('app.event_dispatcher', new ContainerAwareEventDispatcher($container));
    $loader = new YamlFileLoader($container, $fileLocator);
    $loader->load('config/services.yml');

    $application->setCatchExceptions(true);
    $application->add($container->get('deploy_test_branch_command'));
    $application->setDefaultCommand(DeployTestBranchCommand::NAME);

    $application->run();
} catch (Exception $exception) {
    (new \SimpleLogger\Stdout())->critical($exception->getMessage());
    exit(1);
}
