<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Task\CreateTicketDirTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use FileNotFoundException;
use Nette\DirectoryNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

/**
 * @covers \AppBuilder\Application\Module\TaskManager\Task\CreateTicketDirTask
 */
class CreateTicketDirTaskTest extends TestCase
{
    /** @var Parameters | PHPUnit_Framework_MockObject_MockObject */
    private $applicationParams;

    /** @var FileManagerService | PHPUnit_Framework_MockObject_MockObject */
    private $fileManager;

    /** @var string */
    private $path;

    /** @var Ticket | PHPUnit_Framework_MockObject_MockObject */
    private $ticket;

    /** @var CreateTicketDirTask */
    private $task;

    protected function setUp() : void
    {
        $this->path              = __DIR__ . '/var/www/test/key';
        $this->applicationParams = $this->createMock(Parameters::class);
        $this->fileManager       = $this->getMockBuilder(FileManagerService::class)
            ->setConstructorArgs([$this->createMock(LoggerInterface::class)])
            ->setMethods(['createDir', 'changeDir', 'createSymlink'])
            ->getMock();
        $this->ticket = $this->createMock(Ticket::class);
        $this->task   = new CreateTicketDirTask($this->ticket, $this->applicationParams, $this->fileManager);
    }

    /**
     * @test
     */
    public function willThrowFileNotFoundException() : void
    {
        $this->ticket->method('key')->willReturn('key');

        $this
            ->fileManager
            ->method('createDir')
            ->willReturn(false);

        $this->expectException(FileNotFoundException::class);

        $this->task->execute();
    }

    /**
     * @test
     */
    public function willThrowDirectoryNotFoundException() : void
    {
        $this->fileManager->method('changeDir')->willThrowException(new DirectoryNotFoundException());
        $this->fileManager->method('createDir')->willReturn(true);

        $this->expectException(DirectoryNotFoundException::class);

        $this->task->execute();
    }

    /**
     * @test
     */
    public function executeWillReturnTrue() : void
    {
        $path = __DIR__ . '/testdir';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $this->applicationParams->method('path')->willReturn($path);
        $this->applicationParams->method('snapshotPath')->willReturn($path . '/snap.txt');
        $this->ticket->method('ticketStatus')->willReturn('Done');
        $this->fileManager->method('createSymlink')->willReturn(true);
        $this->fileManager->method('createDir')->willReturn(true);

        $task = new CreateTicketDirTask($this->ticket, $this->applicationParams, $this->fileManager);

        $this->assertTrue($task->execute());

        unlink($path . '/snap.txt');
        rmdir($path);
    }
}
