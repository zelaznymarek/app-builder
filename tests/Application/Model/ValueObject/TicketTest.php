<?php

declare(strict_types=1);

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
        $this->assertSame($ticketData['status'], $ticket->ticketStatus());
        $this->assertSame($ticketData['project'], $ticket->project());
        $this->assertSame($prData['pull_request_url'], $ticket->url());
        $this->assertSame($prData['pull_request_name'], $ticket->pullRequestName());
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

    public function validDataProvider() : array
    {
        return [
            'validData1' => [
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
                ],
                [
                    'ticketExists' => null,
                    'ticketDir'    => '/dir/id',
                ],
            ],
        ];
    }
}
