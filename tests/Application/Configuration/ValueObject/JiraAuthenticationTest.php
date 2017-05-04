<?php

declare(strict_types=1);

namespace Tests\Application\Configuration\ValueObject;

use PHPUnit\Framework\TestCase;
use Pvg\Application\Configuration\ValueObject\JiraAuthentication;

/**
 * @coversNothing
 */
class JiraAuthenticationTest extends TestCase
{
    public function testCreatingCorrect() : void
    {
        $credentials = new JiraAuthentication('host', 'username', 'password');

        $this->assertInstanceOf(JiraAuthentication::class, $credentials);
    }
}
