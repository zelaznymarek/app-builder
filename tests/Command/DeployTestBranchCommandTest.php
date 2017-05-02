<?php

namespace Tests\Command;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Command\DeployTestBranchCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeployTestBranchCommandTest extends TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $configArray;

    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    /** @var DeployTestBranchCommand */
    private $deployTestBranchCommand;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->configArray = [
            'host' => 'host',
            'username' => 'user',
            'password' => 'pass'
        ];
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->deployTestBranchCommand = new DeployTestBranchCommand(
            $this->configArray,
            $this->logger,
            $this->dispatcher);
    }

    public function testCreatingCorrect() : void
    {
        $this->assertInstanceOf(DeployTestBranchCommand::class, $this->deployTestBranchCommand);
    }

}