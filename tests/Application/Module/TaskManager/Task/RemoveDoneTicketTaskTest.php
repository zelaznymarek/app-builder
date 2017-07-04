<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TaskManager\Task;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Task\RemoveFinishedTicketTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \AppBuilder\Application\Module\TaskManager\Task\RemoveFinishedTicketTask
 */
class RemoveDoneTicketTaskTest extends TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @test
     */
    public function executeWillReturnTrue() : void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        /** @var Ticket */
        $ticket = $this->createMock(Ticket::class);

        $ticket->method('key')->willReturn('key');

        /** @var Parameters */
        $applicaionParams = $this->createMock(Parameters::class);

        $applicaionParams->method('projectsHomeDir')->willReturn(__DIR__ . '/');
        $applicaionParams->method('symlinkTarget')->willReturn(__DIR__ . '/symlink');

        /** @var FileManagerService */
        $fileManager = new FileManagerService($this->logger);

        /** @var RemoveFinishedTicketTask */
        $task = new RemoveFinishedTicketTask($ticket, $applicaionParams, $fileManager);

        $projectHomeDir = __DIR__ . '/key';
        if (!is_dir($projectHomeDir)) {
            mkdir($projectHomeDir);
        }

        $publicHostDir = __DIR__ . '/symlink';
        if (!is_dir($publicHostDir)) {
            mkdir($publicHostDir);
        }

        touch($projectHomeDir . '/file.txt');

        $this->assertTrue($task->execute());

        if (is_dir($projectHomeDir)) {
            rmdir($projectHomeDir);
        }

        if (is_dir($publicHostDir)) {
            rmdir($publicHostDir);
        }
    }
}
