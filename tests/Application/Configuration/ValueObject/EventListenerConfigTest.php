<?php

declare(strict_types=1);

namespace Tests\Application\Configuration\ValueObject;

use PHPUnit\Framework\TestCase;
use Pvg\Application\Configuration\ValueObject\EventListenerConfig;

/**
 * @covers \Pvg\Application\Configuration\ValueObject\EventListenerConfig
 */
class EventListenerConfigTest extends TestCase
{
    public function testCreatingCorrect() : void
    {
        $config = new EventListenerConfig('an-event', 'a-service', 'do-something');

        $this->assertInstanceOf(EventListenerConfig::class, $config);
    }
}
