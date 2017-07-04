<?php

declare(strict_types = 1);

namespace Tests\Application\Module\TaskManager\Factory;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Model\ValueObject\Ticket;
use AppBuilder\Application\Module\TaskManager\Factory\TaskFactory;
use AppBuilder\Application\Module\TaskManager\Task\CreateTicketDirTask;
use AppBuilder\Application\Module\TaskManager\Task\NoActionTask;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use FileNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

/**
 * @covers \AppBuilder\Application\Module\TaskManager\Factory\TaskFactory
 */
class TaskFactoryTest extends TestCase
{
    /** @var LoggerInterface | PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var Parameters */
    private $applicationParams;

    /** @var FileManagerService | PHPUnit_Framework_MockObject_MockObject */
    private $fileManager;

    protected function setUp() : void
    {
        $this->logger            = $this->createMock(LoggerInterface::class);
        $this->applicationParams = $this->createMock(Parameters::class);
        $this->fileManager       = $this->createMock(FileManagerService::class);
    }

    /**
     * @test
     * @dataProvider createDirData
     */
    public function willReturnCreateTicketDirTask(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var TaskFactory */
        $taskFactory = new TaskFactory($this->logger, $this->applicationParams, $this->fileManager);

        $this->assertSame(CreateTicketDirTask::class, get_class($taskFactory->create($ticket)));
    }

    /**
     * @test
     * @dataProvider noActionData
     */
    public function willReturnNoActionTask(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var TaskFactory */
        $taskFactory = new TaskFactory($this->logger, $this->applicationParams, $this->fileManager);

        $this->assertSame(NoActionTask::class, get_class($taskFactory->create($ticket)));
    }

    /**
     * @test
     * @dataProvider updateTicketData
     */
    public function willThrowFileNotFoundException(
        array $jiraTicket,
        array $pullRequest,
        array $dir
    ) : void {
        /** @var Ticket */
        $ticket = new Ticket($jiraTicket, $pullRequest, $dir);

        /** @var TaskFactory */
        $taskFactory = new TaskFactory($this->logger, $this->applicationParams, $this->fileManager);

        $this
            ->fileManager
            ->method('combineFilepath')
            ->willThrowException(new FileNotFoundException());

        $this
            ->logger
            ->expects($this->once())
            ->method('critical')
            ->withAnyParameters();

        $taskFactory->create($ticket);
    }

    public function createDirData() : array
    {
        $jiraTicket = [
            'id'                    => 10,
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

    public function noActionData() : array
    {
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

    public function updateTicketData() : array
    {
        $jiraTicket = [
            'id'                    => 30,
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
            'ticketExists' => true,
            'ticketDir'    => '/dir',
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
}
