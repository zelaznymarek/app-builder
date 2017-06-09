<?php

declare(strict_types = 1);

namespace Tests\Application\Model\ValueObject;

use PHPUnit\Framework\TestCase;
use Pvg\Application\Model\Exception\NullArgumentException;
use Pvg\Application\Model\ValueObject\Ticket;

/**
 * @covers \Pvg\Application\Model\ValueObject\Ticket
 */
class TicketTest extends TestCase
{
    /**
     * @test
     * @dataProvider validDataProvider
     */
    public function createProperTicket(
        array $ticketData,
        array $prData,
        array $dirData
    ) : void {
        $ticket = new Ticket($ticketData, $prData, $dirData);

        $this->assertSame($ticketData['id'], $ticket->id());
        $this->assertSame($ticketData['ticket_key'], $ticket->key());
        $this->assertSame($ticketData['assignee_name'], $ticket->assigneeName());
        $this->assertSame($ticketData['assignee_display_name'], $ticket->assigneeDisplayName());
        $this->assertSame($ticketData['assignee_email'], $ticket->assigneeEmail());
        $this->assertTrue($ticket->isAssigneeActive());
        $this->assertSame($ticketData['status'], $ticket->ticketStatus());
        $this->assertSame($ticketData['status_category'], $ticket->ticketStatusCategory());
        $this->assertSame($ticketData['components'], $ticket->components());
        $this->assertSame($ticketData['ticket_type'], $ticket->type());
        $this->assertSame($ticketData['project'], $ticket->project());
        $this->assertSame($ticketData['fix_version'], $ticket->fixVersion());
        $this->assertSame($ticketData['summary'], $ticket->summary());
        $this->assertTrue($ticket->hasBranch());
        $this->assertTrue($ticket->isDone());
        $this->assertSame($prData['pull_request_branch'], $ticket->branch());
        $this->assertSame($prData['pull_request_last_update'], $ticket->lastUpdate());
        $this->assertSame($prData['pull_request_url'], $ticket->url());
        $this->assertSame($prData['pull_request_status'], $ticket->pullRquestStatus());
        $this->assertSame($prData['pull_request_name'], $ticket->pullRequestName());
        $this->assertSame($prData['repository'], $ticket->repository());
        $this->assertSame($dirData['ticketExists'], $ticket->hasDirectory());
        $this->assertSame($dirData['ticketDir'], $ticket->directory());
    }

    /**
     * @test
     * @dataProvider invalidDataProvider
     */
    public function nullArgumentExceptionExpected(
        array $ticketData,
        array $prData,
        array $dirData
    ) : void {
        $this->expectException(NullArgumentException::class);
        new Ticket($ticketData, $prData, $dirData);
    }

    /**
     * @test
     * @dataProvider validDataProvider
     */
    public function willReturnSameStatusAsGiven(
        array $ticketData,
        array $prData,
        array $dirData
    ) : void {
        $ticket = new Ticket($ticketData, $prData, $dirData);
        $this->assertTrue($ticket->compareStatus($ticketData['status']));
    }

    /**
     * @test
     * @dataProvider noBranchDataProvider
     */
    public function hasBranchIsFalseWhenFalseGiven(
        array $ticketData,
        array $prData,
        array $dirData
    ) : void {
        $ticket = new Ticket($ticketData, $prData, $dirData);
        $this->assertFalse($ticket->hasBranch());
    }

    public function noBranchDataProvider() : array
    {
        return [
            'validData1' => [
                [
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
                [
                    'pull_request_branch'      => null,
                    'pull_request_last_update' => null,
                    'pull_request_url'         => null,
                    'pull_request_status'      => null,
                    'pull_request_name'        => null,
                    'repository'               => null,
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
        ];
    }

    public function validDataProvider() : array
    {
        return [
            'validData1' => [
                [
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
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => null,
                    'pull_request_status'      => null,
                    'pull_request_name'        => null,
                    'repository'               => null,
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
            'validData2' => [
                [
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
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'www.url.com',
                    'pull_request_status'      => 'Merged',
                    'pull_request_name'        => 'key',
                    'repository'               => 'repo',
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
        ];
    }

    public function invalidDataProvider() : array
    {
        return [
            'invalidData1' => [
                [
                    'id'                    => null,
                    'ticket_key'            => 'key',
                    'assignee_name'         => '',
                    'assignee_display_name' => '',
                    'assignee_email'        => '',
                    'assignee_active'       => null,
                    'status'                => 'Done',
                    'status_category'       => 'Done',
                    'components'            => '',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'www.url.com',
                    'pull_request_status'      => 'Merged',
                    'pull_request_name'        => 'key',
                    'repository'               => 'repo',
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
            'invalidData2' => [
                [
                    'id'                    => 20,
                    'ticket_key'            => null,
                    'assignee_name'         => '',
                    'assignee_display_name' => '',
                    'assignee_email'        => '',
                    'assignee_active'       => null,
                    'status'                => 'Done',
                    'status_category'       => 'Done',
                    'components'            => '',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'www.url.com',
                    'pull_request_status'      => 'Merged',
                    'pull_request_name'        => 'key',
                    'repository'               => 'repo',
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
            'invalidData3' => [
                [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => '',
                    'assignee_display_name' => '',
                    'assignee_email'        => '',
                    'assignee_active'       => null,
                    'status'                => null,
                    'status_category'       => 'Done',
                    'components'            => '',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'www.url.com',
                    'pull_request_status'      => 'Merged',
                    'pull_request_name'        => 'key',
                    'repository'               => 'repo',
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
            'invalidData4' => [
                [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => '',
                    'assignee_display_name' => '',
                    'assignee_email'        => '',
                    'assignee_active'       => null,
                    'status'                => 'Done',
                    'status_category'       => 'Done',
                    'components'            => '',
                    'ticket_type'           => 'Fix',
                    'project'               => null,
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'www.url.com',
                    'pull_request_status'      => 'Merged',
                    'pull_request_name'        => 'key',
                    'repository'               => 'repo',
                ],
                [
                    'ticketExists' => true,
                    'ticketDir'    => '/dir/id',
                ],
            ],
            'invalidData5' => [
                [
                    'id'                    => 20,
                    'ticket_key'            => 'key',
                    'assignee_name'         => '',
                    'assignee_display_name' => '',
                    'assignee_email'        => '',
                    'assignee_active'       => null,
                    'status'                => 'Done',
                    'status_category'       => 'Done',
                    'components'            => '',
                    'ticket_type'           => 'Fix',
                    'project'               => 'project',
                    'fix_version'           => '1.0',
                    'summary'               => 'Summary...',
                ],
                [
                    'pull_request_branch'      => 'branch',
                    'pull_request_last_update' => '2017',
                    'pull_request_url'         => 'www.url.com',
                    'pull_request_status'      => 'Merged',
                    'pull_request_name'        => 'key',
                    'repository'               => 'repo',
                ],
                [
                    'ticketExists' => null,
                    'ticketDir'    => '/dir/id',
                ],
            ],
        ];
    }
}
