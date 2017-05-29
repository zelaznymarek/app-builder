<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use Symfony\Component\EventDispatcher\Event;

class TicketDirIndexedEvent extends Event
{
    /** @var string */
    public const NAME = 'ticket.dir.indexed';

    /** @var array */
    private $indexedDir;

    public function __construct(array $indexedDir)
    {
        $this->indexedDir = $indexedDir;
    }

    public function indexedDir() : array
    {
        return $this->indexedDir;
    }
}
