<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Task\UpdateTicketTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

/**
 * @covers \AppBuilder\Application\Module\TaskManager\Task\UpdateTicketTask
 */
class UpdateTicketTaskTest extends TestCase
{
    /** @var Ticket | PHPUnit_Framework_MockObject_MockObject */
    private $ticket;

    /** @var Parameters | PHPUnit_Framework_MockObject_MockObject */
    private $applicationParams;

    /** @var FileManagerService | PHPUnit_Framework_MockObject_MockObject */
    private $fileManager;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp() : void
    {
        $this->ticket            = $this->createMock(Ticket::class);
        $this->applicationParams = $this->createMock(Parameters::class);
        $this->logger            = $this->createMock(LoggerInterface::class);

        $this->fileManager = $this->getMockBuilder(FileManagerService::class)
            ->setConstructorArgs([$this->logger])
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @test
     */
    public function executeWillReturnTrue() : void
    {
        $file = __DIR__ . '/snap.txt';
        $this->ticket->method('ticketStatus')->willReturn('status');
        $this->ticket->method('branch')->willReturn('branch');
        $this->applicationParams->method('snapshotPath')->willReturn($file);
        $this->applicationParams->method('path')->willReturn(__DIR__);

        $task = new UpdateTicketTask($this->ticket, $this->applicationParams, $this->fileManager);

        $this->assertTrue($task->execute());

        if (file_exists($file)) {
            unlink($file);
        }
    }
}
