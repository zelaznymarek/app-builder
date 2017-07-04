<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Exception\MisguidedTaskException;
use AppBuilder\Application\Module\TaskManager\Factory\CreateTicketDirTaskFactory;
use AppBuilder\Application\Module\TaskManager\Task\CreateTicketDirTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AppBuilder\Application\Module\TaskManager\Factory\CreateTicketDirTaskFactory
 */
class CreateTicketDirTaskFactoryTest extends TestCase
{
    /** @var Parameters */
    private $applicationParams;

    /** @var FileManagerService */
    private $fileManager;

    protected function setUp() : void
    {
        $this->applicationParams = $this->createMock(Parameters::class);
        $this->fileManager       = $this->createMock(FileManagerService::class);
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
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var CreateTicketDirTaskFactory */
        $task = new CreateTicketDirTaskFactory();

        $this->assertSame(
            CreateTicketDirTask::class,
            get_class($task->create($ticket, $this->applicationParams, $this->fileManager))
        );
    }

    /**
     * @test
     * @dataProvider invalidData
     */
    public function willThrowException(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var CreateTicketDirTaskFactory */
        $task = new CreateTicketDirTaskFactory();

        $this->expectException(MisguidedTaskException::class);

        $task->create($ticket, $this->applicationParams, $this->fileManager);
    }

    public function validData() : array
    {
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
        ];
        $pullRequest = [
            'pull_request_branch'      => 'branch',
            'pull_request_last_update' => '2017',
            'pull_request_url'         => 'url',
            'pull_request_status'      => 'Fix',
            'pull_request_name'        => 'pull req',
            'repository'               => 'repo',
        ];
        $dir = [
            'ticketExists' => false,
            'ticketDir'    => null,
            'ticketId'     => 20,
        ];

        return [
            'data1' => [
                $jiraTicket,
                $pullRequest,
                $dir,
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
                    'ticketDir'    => 'dir',
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
                    'status'                => 'Done',
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
}
