<?php

declare(strict_types = 1);

namespace Tests\Application\Configuration;

use PHPUnit\Framework\TestCase;
use Pvg\Application\Configuration\JiraAuthentication;

/**
 * @covers \Pvg\Application\Configuration\JiraAuthentication
 */
class JiraAuthenticationTest extends TestCase
{
    /**
     * @test
     */
    public function creatingCorrect() : void
    {
        $credentials = new JiraAuthentication('host', 'username', 'password');

        $this->assertInstanceOf(JiraAuthentication::class, $credentials);
    }
}
