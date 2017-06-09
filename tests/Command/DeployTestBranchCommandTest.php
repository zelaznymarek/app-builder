<?php

declare(strict_types = 1);

namespace Tests\Command;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Command\DeployTestBranchCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Pvg\Command\DeployTestBranchCommand
 */
class DeployTestBranchCommandTest extends TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Parameters | \PHPUnit_Framework_MockObject_MockObject */
    private $applicationParams;

    /** @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    /** @var DeployTestBranchCommand */
    private $deployTestBranchCommand;

    public function setUp() : void
    {
        $this->logger                  = $this->createMock(LoggerInterface::class);
        $this->applicationParams       = $this->createMock(Parameters::class);
        $this->dispatcher              = $this->createMock(EventDispatcherInterface::class);
        $this->deployTestBranchCommand = new DeployTestBranchCommand(
            $this->applicationParams,
            $this->logger,
            $this->dispatcher);
    }

    /**
     * @test
     */
    public function creatingCorrect() : void
    {
        $this->assertInstanceOf(DeployTestBranchCommand::class, $this->deployTestBranchCommand);
    }
}
