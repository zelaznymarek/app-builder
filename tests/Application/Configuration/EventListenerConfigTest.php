<?php

declare(strict_types = 1);

namespace Tests\Application\Configuration;

use AppBuilder\Application\Configuration\EventListenerConfig;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AppBuilder\Application\Configuration\EventListenerConfig
 */
class EventListenerConfigTest extends TestCase
{
    /** @var EventListenerConfig */
    private $eventListenerConfig;

    protected function setUp() : void
    {
        $this->eventListenerConfig = new EventListenerConfig('an-event', 'a-service', 'do-something');
    }

    /**
     * @test
     */
    public function willCreateListenerConfigFromConfigArray() : void
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

    /**
     * @test
     */
    public function willThrowExceptionCauseOfEmptyEvent() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event name cannot be empty');

        new EventListenerConfig('', 'an-id', 'an-action');
    }

    /**
     * @test
     */
    public function willThrowExceptionCauseOfEmptyId() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service ID (from service container) cannot be empty!');

        new EventListenerConfig('an-event', '', 'an-action');
    }

    /**
     * @test
     */
    public function willThrowExceptionCauseOfEmptyAction() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service listener method cannot be empty');

        new EventListenerConfig('an-event', 'an-id', '');
    }
}
