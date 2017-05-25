<?php

declare(strict_types=1);

namespace Pvg\Application\Module\TicketAggregate;

use Psr\Log\LoggerInterface;
use Pvg\Application\Module\TicketAggregate\Factory\TicketServiceFactory;
use Pvg\Event\Application\BitbucketTicketMappedEvent;
use Pvg\Event\Application\BitbucketTicketMappedEventAware;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEventAware;
use Pvg\Event\Application\TicketDirIndexedEvent;
use Pvg\Event\Application\TicketDirIndexedEventAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TicketServiceRepository implements
    JiraTicketMappedEventAware,
    BitbucketTicketMappedEventAware,
    TicketDirIndexedEventAware
{
    /** @var array */
    private $ticketServices = [];

    /** @var TicketServiceFactory */
    private $factory;

    /** @var TicketBuilder */
    private $builder;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        TicketServiceFactory $factory,
        TicketBuilder $builder,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->factory    = $factory;
        $this->builder    = $builder;
        $this->dispatcher = $dispatcher;
        $this->logger     = $logger;
    }

    /**
     * Returns ticket service, adequate to 'id' passed as parameter, from ticketService repository.
     * If passed 'id' is not in repository, creates new ticket service, adds to repository and returns it.
     */
    public function get(int $id) : TicketService
    {
        if (array_key_exists($id, $this->ticketServices)) {
            return $this->ticketServices[$id];
        }
        $ticketService = $this->factory->create(
            $this->builder,
            $this->dispatcher,
            $this->logger
        );
        $this->ticketServices[$id] = $ticketService;

        return $ticketService;
    }

    /**
     * Gets adequate ticketService from repository and passes event to it.
     */
    public function onBitbucketTicketMapped(BitbucketTicketMappedEvent $event) : void
    {
        $ticketService = $this->get(array_keys($event->bitbucketTicket())['0']);
        $ticketService->onBitbucketTicketMapped($event);
    }

    /**
     * Gets adequate ticketService from repository and passes event to it.
     */
    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $ticketService = $this->get($event->ticket()['id']);
        $ticketService->onJiraTicketMapped($event);
    }

    /**
     * Gets adequate ticketService from repository and passes event to it.
     */
    public function onTicketDirIndexed(TicketDirIndexedEvent $event) : void
    {
        $ticketService = $this->get($event->indexedDir()['ticketId']);
        $ticketService->onTicketDirIndexed($event);
    }
}
