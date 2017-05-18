<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira;

use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\Exception\InvalidJiraStatusException;
use Pvg\Application\Module\Jira\ValueObject\JiraTicketStatus;
use Pvg\Application\Utils\Mapper\JiraMapperCreator;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Pvg\Event\Application\ApplicationInitializedEventAware;
use Pvg\Event\Application\TicketsFetchedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TypeError;

class ExternalLibraryJiraService implements
    JiraService,
    ApplicationInitializedEventAware
{
    /** @var int */
    private const MAX_RESULTS = 100;

    /** @var IssueService */
    private $issueService;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var QueryRepository */
    private $queryRepository;

    public function __construct(
        IssueService $issueService,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
        QueryRepository $queryRepository
    ) {
        $this->issueService    = $issueService;
        $this->logger          = $logger;
        $this->dispatcher      = $dispatcher;
        $this->queryRepository = $queryRepository;
    }

    /**
     * Service connects to JIRA with specified credentials when event occurs.
     * If connected successfully, fetches all tickets from JIRA.
     */
    public function onApplicationInitialized(ApplicationInitializedEvent $event = null) : void
    {
        if (!$this->validateCredentials()) {
            $this->logger->warning('Invalid login or password');
        }
        $this->fetchAllTickets();
    }

    /**
     * Method uses provided credentials to connect JIRA.
     * If successful returns true. If credentials are invalid.
     * JiraException is thrown.
     */
    public function validateCredentials() : bool
    {
        try {
            $this->issueService->search($this->queryRepository->validateCredentials());

            return true;
        } catch (JiraException $e) {
            return false;
        }
    }

    /**
     * Fetches tickets with passed status and dispatches TicketsFetchedEvent with tickets passed.
     */
    public function fetchTicketsByStatus(string $status) : void
    {
        $tickets = null;
        try {
            $tickets = $this
                ->sendQuery($this
                    ->queryRepository
                    ->fetchTicketsByStatus(JiraTicketStatus::createFromString($status)->status())
                );
        } catch (InvalidJiraStatusException $e) {
            $this->logger->info('Error: ' . $e->getMessage());
        }

        try {
            $this->issueSearchResultToArray($tickets);
        } catch (TypeError $e) {
            $this->logger->warning('Error. Fetching method returned null');
        }
    }

    /**
     * Fetches all tickets and dispatches TicketsFetchedEvent with tickets passed.
     */
    public function fetchAllTickets() : void
    {
        $tickets = $this
            ->sendQuery($this
                ->queryRepository
                ->fetchAllTickets()
            );

        $this->logger->info('Tickets fetched.');
        try {
            $this->issueSearchResultToArray($tickets);
        } catch (TypeError $e) {
            $this->logger->warning('Error. Fetching method returned null');
        }
    }

    /**
     * Sends passed JQL query to JIRA and returns IssueSearchResult object.
     */
    private function sendQuery(string $jql) : ?IssueSearchResult
    {
        try {
            return $this->issueService->search($jql, 0, self::MAX_RESULTS);
        } catch (JiraException $e) {
            $this->logger->info('Search Failed : ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Converts IssueSearchResult to array.
     */
    private function issueSearchResultToArray(IssueSearchResult $isr) : void
    {
        $ticketsArray = [];
        foreach ($isr->issues as $issue) {
            $ticketsArray[$issue->key] = json_decode(json_encode($issue), true);
        }

        $this->mapToJiraTicket($ticketsArray);
    }

    /**
     * Maps and filters array with full Jira ticket into desired array format.
     * This method probably will be moved to separate class.
     */
    private function mapToJiraTicket(array $tickets) : void
    {
        $mappedTickets = [];
        $jiraTickets   = [];
        foreach ($tickets as $key => $value) {
            $jiraTickets[$key] = JiraMapperCreator::createMapper();
            foreach ($jiraTickets[$key] as $mapper) {
                $mappedTickets[$key][$mapper->outputKey()] = $mapper->map($value);
            }
        }
        $this->logger->info('Tickets mapped.');
        foreach ($mappedTickets as $mappedTicket) {
            $this->dispatchTicketsFetchedEvent($mappedTicket);
        }
    }

    /**
     * Dispatches TicketsFetchedEvent.
     */
    private function dispatchTicketsFetchedEvent(array $ticket) : void
    {
        $this->dispatcher->dispatch(
            TicketsFetchedEvent::NAME,
            new TicketsFetchedEvent($ticket)
        );
    }
}
