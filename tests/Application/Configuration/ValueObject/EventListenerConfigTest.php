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
    /** @var EventListenerConfig */
    private $config;

    public function setUp() : void
    {
        $this->config = new EventListenerConfig('an-event', 'a-service', 'do-something');
    }

    public function testCreatingCorrect() : void
    {
        $config = new EventListenerConfig('an-event', 'a-service', 'do-something');

        $this->assertInstanceOf(EventListenerConfig::class, $config);
    }

    public function testCreateFromConfigArray() : void
    {
        $array = [
            'some_event' => [
                'some_listener' => [
                    'priority' => 1,
                    'service'  => 'some_service',
                    'action'   => 'some_action',
                ],
            ],
        ];
        $result = EventListenerConfig::createFromConfigArray($array);

        foreach ($result as $event) {
            $this->assertSame($event->event(), key($array));
            $this->assertSame($event->action(), $array['some_event']['some_listener']['action']);
            $this->assertSame($event->priority(), $array['some_event']['some_listener']['priority']);
        }
    }
}
