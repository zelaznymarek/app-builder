<?php

declare(strict_types = 1);

namespace Tests\Application\Configuration;

use AppBuilder\Application\Configuration\JiraAuthentication;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AppBuilder\Application\Configuration\JiraAuthentication
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
