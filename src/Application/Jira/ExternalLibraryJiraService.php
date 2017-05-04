<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;
use Pvg\Application\Jira\Exception\InvalidJiraStatusException;
use Pvg\Application\Jira\ValueObject\JiraTicketStatus;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Pvg\Event\Application\ApplicationInitializedEventAware;
use Pvg\Event\Application\TicketsFetchedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryJiraService implements
    JiraService,
    ApplicationInitializedEventAware
{
    /** @var int */
    private const MAX_RESULTS = 500;
    /** @var IssueService */
    private $jiraService;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var QueryRepository */
    private $queryRepository;

    public function __construct(
        IssueService $jiraIssueService,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
        QueryRepository $queryRepository
    ) {
        $this->jiraService       = $jiraIssueService;
        $this->logger            = $logger;
        $this->dispatcher        = $dispatcher;
        $this->queryRepository   = $queryRepository;
    }

    /**
     * Service connects to JIRA with specified credentials when event occurs.
     * If connected successfully, fetches all tickets from JIRA.
     */
    public function onApplicationInitialized(ApplicationInitializedEvent $event = null) : void
    {
        if ($this->login()) {
            $this->fetchAllTickets();
        }
    }

    /**
     * Method uses provided credentials to connect JIRA.
     * If successful returns true. If credentials are invalid
     * JiraException is thrown.
     */
    public function login() : bool
    {
        $this->jiraService->search($this->queryRepository->validateCredentials());

        return true;
    }

    /**
     * Fetches tickets with passed status and dispatches TicketsFetchedEvent with tickets passed.
     */
    public function fetchTicketsByStatus(string $status) : void
    {
        try {
            $tickets = $this->sendQuery(
                $this->
                queryRepository->
                fetchTicketsByStatus(JiraTicketStatus::createFromString($status)->status())
            );
            $this->dispatchTicketsFetchedEvent($tickets);
        } catch (InvalidJiraStatusException $e) {
            $this->logger->info('Error: ' . $e->getMessage());
        }
    }

    /**
     * Fetches all tickets and dispatches TicketsFetchedEvent with tickets passed.
     */
    public function fetchAllTickets() : void
    {
        $tickets = $this->sendQuery(
            $this
                ->queryRepository
                ->fetchAllTickets()
        );
        $this->dispatchTicketsFetchedEvent($tickets);
    }

    /**
     * Sends passed JQL query to JIRA and returns IssueSearchResult object.
     */
    private function sendQuery(string $jql) : ?IssueSearchResult
    {
        try {
            return $this->jiraService->search($jql, 0, self::MAX_RESULTS);
        } catch (JiraException $e) {
            $this->logger->info('Search Failed : ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Dispatches TicketsFetchedEvent.
     */
    private function dispatchTicketsFetchedEvent(IssueSearchResult $tickets) : void
    {
        $this->dispatcher->dispatch(
            TicketsFetchedEvent::NAME,
            new TicketsFetchedEvent($tickets)
        );
    }
}
