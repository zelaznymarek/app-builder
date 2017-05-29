<?php

declare(strict_types=1);

namespace Pvg\Application\Configuration;

class EventListenerConfig
{
    /** @var string */
    private $event;

    /** @var string */
    private $listenerServiceId;

    /** @var string */
    private $action;

    /** @var int */
    private $priority;

    /**
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $event,
        string $listenerServiceId,
        string $action,
        int $priority = 0
    ) {
        $this
            ->setEvent($event)
            ->setListenerServiceId($listenerServiceId)
            ->setAction($action);
        $this->priority = $priority;
    }

    public function event() : string
    {
        return $this->event;
    }

    public function listenerServiceId() : string
    {
        return $this->listenerServiceId;
    }

    public function action() : string
    {
        return $this->action;
    }

    public function priority() : int
    {
        return $this->priority;
    }

    /**
     * @return static[]
     *
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public static function createFromConfigArray(array $configArray) : array
    {
        $result = [];
        foreach ($configArray as $event => $listeners) {
            if (empty($listeners)) {
                continue;
            }
            foreach ($listeners as $listener) {
                $priority = $listener['priority'] ?? 0;
                $result[] = new static($event, $listener['service'], $listener['action'], $priority);
            }
        }

        return $result;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function setEvent(string $event) : self
    {
        if (empty($event)) {
            throw new \InvalidArgumentException('Event name cannot be empty');
        }
        $this->event = $event;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function setListenerServiceId(string $listenerServiceId) : self
    {
        if (empty($listenerServiceId)) {
            throw new \InvalidArgumentException('Service ID (from service container) cannot be empty!');
        }
        $this->listenerServiceId = $listenerServiceId;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function setAction(string $action) : self
    {
        if (empty($action)) {
            throw new \InvalidArgumentException('Service listener method cannot be empty');
        }
        $this->action = $action;

        return $this;
    }
}
