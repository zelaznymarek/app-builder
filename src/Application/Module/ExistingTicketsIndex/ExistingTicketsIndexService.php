<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\ExistingTicketsIndex;

use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\Parameters;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEventAware;
use Pvg\Event\Application\TicketDirIndexedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExistingTicketsIndexService implements JiraTicketMappedEventAware
{
    /** @var string */
    private $ticketKey;

    /** @var string */
    private $homeDir;

    /** @var ?string */
    private $ticketDir;

    /** @var bool */
    private $ticketExists;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var int */
    private $id;

    /** @var Parameters */
    private $applicationParams;

    public function __construct(
        Parameters $applicationParams,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->applicationParams = $applicationParams;
        $this->homeDir           = $applicationParams->projectsHomeDir();
        $this->dispatcher        = $dispatcher;
        $this->logger            = $logger;
    }

    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $this->ticketKey  = $event->ticket()['ticket_key'];
        $this->id         = $event->ticket()['id'];
        $this->searchForTicketDir();
    }

    /**
     * Looks for application directory in home directory.
     * Returns true and path if found or false and empty string if not.
     */
    private function searchForTicketDir() : void
    {
        $folders = scandir($this->homeDir);
        if (in_array($this->ticketKey, $folders, false)) {
            $this->ticketDir    = $this->applicationParams->path($this->ticketKey);
            $this->ticketExists = true;
        } else {
            $this->ticketDir    = null;
            $this->ticketExists = false;
        }
        $this->dispatcher->dispatch(
            TicketDirIndexedEvent::NAME,
            new TicketDirIndexedEvent([
                'ticketId'     => $this->id,
                'ticketDir'    => $this->ticketDir,
                'ticketExists' => $this->ticketExists,
            ])
        );
    }
}
