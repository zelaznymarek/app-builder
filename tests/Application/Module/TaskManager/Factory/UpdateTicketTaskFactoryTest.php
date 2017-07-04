<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Exception\MisguidedTaskException;
use AppBuilder\Application\Module\TaskManager\Factory\UpdateTicketTaskFactory;
use AppBuilder\Application\Module\TaskManager\Task\UpdateTicketTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use FileNotFoundException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \AppBuilder\Application\Module\TaskManager\Factory\UpdateTicketTaskFactory
 */
class UpdateTicketTaskFactoryTest extends TestCase
{
    /** @var Parameters | PHPUnit_Framework_MockObject_MockObject */
    private $applicationParams;

    /** @var FileManagerService | PHPUnit_Framework_MockObject_MockObject */
    private $fileManager;

    /** @var vfsStreamDirectory */
    private $root;

    /** @var vfsStreamFile */
    private $file;

    protected function setUp() : void
    {
        $this->applicationParams = $this->createMock(Parameters::class);
        $this->fileManager       = $this->createMock(FileManagerService::class);
        $this->root              = vfsStream::setup('virtualDir');
        $this->file              = vfsStream::newFile('file.txt');
    }

    /**
     * @test
     * @dataProvider validData
     */
    public function willReturnTask(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = $this->getMockBuilder(Ticket::class)
            ->setConstructorArgs([$jiraTicket, $pullRequest, $dir])
            ->setMethods(['compareStatus', 'setIsDone'])
            ->getMock();

        $ticket
            ->method('compareStatus')
            ->willReturn(true);

        $this
            ->fileManager
            ->method('fileExists')
            ->willReturn(true);

        $this
            ->fileManager
            ->method('fileGetContent')
            ->willReturn('Work finished;2017-06-09 12:06:41');

        /** @var UpdateTicketTaskFactory */
        $task = new UpdateTicketTaskFactory();

        $this->assertSame(
            UpdateTicketTask::class,
            get_class($task->create($ticket, $this->applicationParams, $this->fileManager))
        );
    }

    /**
     * @test
     * @dataProvider invalidData
     */
    public function willThrowMisguidedTaskException(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var UpdateTicketTaskFactory */
        $task = new UpdateTicketTaskFactory();

        $this->expectException(MisguidedTaskException::class);

        $task->create($ticket, $this->applicationParams, $this->fileManager);
    }

    /**
     * @test
     * @dataProvider noFileData
     */
    public function willThrowFileNotFoundException(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var UpdateTicketTaskFactory */
        $task = new UpdateTicketTaskFactory();

        $this->expectException(FileNotFoundException::class);

        $task->create($ticket, $this->applicationParams, $this->fileManager);
    }

    public function validData() : array
    {
        return [
            'validData1' => [
                $jiraTicket = [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => 'name',
                    'assignee_display_name' => '',
                    'assignee_email'        => 'email',
                    'assignee_active'       => true,
                    'status'                => 'Work finished',
                    'status_category'       => 'Work finished',
                    'components'            => 'Comps',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                $pullRequest = [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'url',
                    'pull_request_status'      => 'Fix',
                    'pull_request_name'        => 'pull req',
                    'repository'               => 'repo',
                ],
                $dir = [
                    'ticketExists' => true,
                    'ticketDir'    => vfsStream::url('virtualDir'),
                    'ticketId'     => 20,
                ],
            ],
        ];
    }

    public function invalidData() : array
    {
        return [
            'invalidData1' => [
                $jiraTicket = [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => 'name',
                    'assignee_display_name' => '',
                    'assignee_email'        => 'email',
                    'assignee_active'       => true,
                    'status'                => 'Work in progress',
                    'status_category'       => 'Done',
                    'components'            => 'Comps',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                $pullRequest = [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'url',
                    'pull_request_status'      => 'Fix',
                    'pull_request_name'        => 'pull req',
                    'repository'               => 'repo',
                ],
                $dir = [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir',
                    'ticketId'     => 20,
                ],
            ],
            'invalidData2' => [
                $jiraTicket = [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => 'name',
                    'assignee_display_name' => '',
                    'assignee_email'        => 'email',
                    'assignee_active'       => true,
                    'status'                => 'Work finished',
                    'status_category'       => 'Done',
                    'components'            => 'Comps',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                $pullRequest = [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'url',
                    'pull_request_status'      => 'Fix',
                    'pull_request_name'        => 'pull req',
                    'repository'               => 'repo',
                ],
                $dir = [
                    'ticketExists' => false,
                    'ticketDir'    => null,
                    'ticketId'     => 20,
                ],
            ],
            'invalidData3' => [
                $jiraTicket = [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => 'name',
                    'assignee_display_name' => '',
                    'assignee_email'        => 'email',
                    'assignee_active'       => true,
                    'status'                => 'Work in progress',
                    'status_category'       => 'Done',
                    'components'            => 'Comps',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                $pullRequest = [
                    'pull_request_branch'      => null,
                    'pull_request_last_update' => null,
                    'pull_request_url'         => null,
                    'pull_request_status'      => null,
                    'pull_request_name'        => null,
                    'repository'               => null,
                ],
                $dir = [
                    'ticketExists' => false,
                    'ticketDir'    => null,
                    'ticketId'     => 20,
                ],
            ],
        ];
    }

    public function noFileData() : array
    {
        return [
            'invalidData1' => [
                $jiraTicket = [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => 'name',
                    'assignee_display_name' => '',
                    'assignee_email'        => 'email',
                    'assignee_active'       => true,
                    'status'                => 'Work finished',
                    'status_category'       => 'Done',
                    'components'            => 'Comps',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                $pullRequest = [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'url',
                    'pull_request_status'      => 'Fix',
                    'pull_request_name'        => 'pull req',
                    'repository'               => 'repo',
                ],
                $dir = [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir',
                    'ticketId'     => 20,
                ],
            ],
        ];
    }
}
