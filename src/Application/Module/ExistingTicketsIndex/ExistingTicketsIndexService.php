<?php

declare(strict_types=1);

namespace Pvg\Application\Module\ExistingTicketsIndex;

use Psr\Log\LoggerInterface;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEventAware;
use Pvg\Event\Application\TicketDirIndexedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExistingTicketsIndexService implements JiraTicketMappedEventAware
{
    /** @var string */
    private $ticketName;

    /** @var string */
    private $homeDir;

    /** @var string */
    private $ticketDir;

    /** @var bool */
    private $ticketExists;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var int */
    private $id;

    public function __construct(
        array $configArray,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->homeDir    = $configArray['parameters']['server.user.project.homedir'];
        $this->dispatcher = $dispatcher;
        $this->logger     = $logger;
    }

    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $this->ticketName = $event->ticket()['ticket_key'];
        $this->id         = $event->ticket()['id'];
        $this->searchForTicketDir();
    }

    private function searchForTicketDir() : void
    {
        $folders = scandir($this->homeDir);
        if (in_array($this->ticketName, $folders, false)) {
            $this->ticketDir    = $this->homeDir . $this->ticketName;
            $this->ticketExists = true;
        } else {
            $this->ticketDir    = '';
            $this->ticketExists = false;
        }
        $this->dispatcher->dispatch(
            TicketDirIndexedEvent::NAME,
            new TicketDirIndexedEvent([
                'ticketId'     => $this->id,
                'ticketDir'    => $this->ticketDir,
                'ticketExists' => $this->ticketExists,
            ]));
    }
}
