<?php

namespace PVG\Application\Configuration\ValueObject;

class EventListenerConfig
{
    /** @var string */
    private $event;
    /** @var string */
    private $listenerServiceId;
    /** @var string */
    private $action;
    /**
     * @var int
     */
    private $priority;

    /**
     * EventListenerConfig constructor.
     *
     * @param string $event
     * @param string $listenerServiceId
     * @param string $action
     * @param int    $priority
     *
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public function __construct($event, $listenerServiceId, $action, $priority = 0)
    {

        $this
            ->setEvent($event)
            ->setListenerServiceId($listenerServiceId)
            ->setAction($action);
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function event()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function listenerServiceId()
    {
        return $this->listenerServiceId;
    }

    /**
     * @return string
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * @param string $event
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function setEvent($event)
    {
        if (empty($event)) {
            throw new \InvalidArgumentException('Event name cannot be empty');
        }
        $this->event = $event;

        return $this;
    }

    /**
     * @param string $listenerServiceId
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function setListenerServiceId($listenerServiceId)
    {
        if (empty($listenerServiceId)) {
            throw new \InvalidArgumentException('Service ID (from service container) cannot be empty!');
        }
        $this->listenerServiceId = $listenerServiceId;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return $this
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function setAction($action)
    {
        if (empty($action)) {
            throw new \InvalidArgumentException('Service listener method cannot be empty');
        }
        $this->action = $action;

        return $this;
    }


    /**
     * @param array $configArray
     *
     * @return $this[]
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public static function createFromConfigArray(array $configArray)
    {
        $result = [];
        foreach ($configArray as $event => $listeners) {
            if (empty($listeners)) {
                continue;
            }
            foreach ($listeners as $listener) {
                $priority = isset($listener['priority'])
                    ? $listener['priority']
                    : 0;
                $result[] = new static($event, $listener['service'], $listener['action'], $priority);
            }
        }

        return $result;
    }
}
