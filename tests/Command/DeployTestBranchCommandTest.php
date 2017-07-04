<?php

declare(strict_types = 1);

namespace Tests\Command;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Command\DeployTestBranchCommand;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \AppBuilder\Command\DeployTestBranchCommand
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

    protected function setUp() : void
    {
        $this->logger                  = $this->createMock(LoggerInterface::class);
        $this->applicationParams       = $this->createMock(Parameters::class);
        $this->dispatcher              = $this->createMock(EventDispatcherInterface::class);
        $this->deployTestBranchCommand = new DeployTestBranchCommand(
            $this->applicationParams,
            $this->logger,
            $this->dispatcher
        );
    }

    /**
     * @test
     */
    public function creatingCorrect() : void
    {
        $this->assertInstanceOf(DeployTestBranchCommand::class, $this->deployTestBranchCommand);
    }
}
