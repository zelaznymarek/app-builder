<?php

declare(strict_types = 1);

namespace Tests\Module\Jira\ValueObject;

use AppBuilder\Application\Module\Jira\Exception\InvalidJiraStatusException;
use AppBuilder\Application\Module\Jira\ValueObject\JiraTicketStatus;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AppBuilder\Application\Module\Jira\ValueObject\JiraTicketStatus
 */
class JiraTicketStatusTest extends TestCase
{
    /**
     * @test
     */
    public function willReturnValidObject() : void
    {
        /** @var JiraTicketStatus */
        $jiraTicketStatus = JiraTicketStatus::createFromString('Done');

        $this->assertSame('Done', $jiraTicketStatus->status());
    }

    /**
     * @test
     */
    public function willThrowInvalidJiraStatusException() : void
    {
        $this->expectException(InvalidJiraStatusException::class);

        /* @var JiraTicketStatus */
        JiraTicketStatus::createFromString('status');
    }
}
