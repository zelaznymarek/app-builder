<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira;

use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\Jira\Exception\InvalidJiraStatusException;
use Pvg\Application\Module\Jira\Exception\NullResultReturned;
use Pvg\Application\Module\Jira\ValueObject\JiraTicketStatus;
use Pvg\Application\Utils\Mapper\JiraMapperCreator;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Pvg\Event\Application\ApplicationInitializedEventAware;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryJiraService implements
    JiraService,
    ApplicationInitializedEventAware
{
    /** @var int */
    private const MAX_RESULTS = 1;

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
        } else {
            try {
                $this->fetchAllTickets();
            } catch (NullResultReturned $e) {
                $this->logger->warning($e->getMessage());
            }
        }
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
     * Fetches tickets with passed status and dispatches JiraTicketMappedEvent with tickets passed.
     *
     * @throws NullResultReturned
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
            $this->logger->warning('Error: ' . $e->getMessage());
        }

        $this->logger->info('Tickets fetched.');

        if (null === $tickets) {
            throw new NullResultReturned('Error. Fetching method returned null');
        }
        $this->issueSearchResultToArray($tickets);
    }

    /**
     * Fetches all tickets and dispatches JiraTicketMappedEvent with tickets passed.
     *
     * @throws NullResultReturned
     */
    public function fetchAllTickets() : void
    {
        $tickets = $this
            ->sendQuery($this
                ->queryRepository
                ->fetchAllTickets()
            );

        $this->logger->info('Tickets fetched.');
        if (null === $tickets) {
            throw new NullResultReturned('Error. Fetching method returned null');
        }
        $this->issueSearchResultToArray($tickets);
    }

    /**
     * Sends passed JQL query to JIRA and returns IssueSearchResult object.
     */
    private function sendQuery(string $jql) : ?IssueSearchResult
    {
        try {
            return $this->issueService->search($jql, 0, self::MAX_RESULTS);
        } catch (JiraException $e) {
            $this->logger->warning('Search Failed : ' . $e->getMessage());
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
        $mappedTickets   = [];
        $ticketMappers   = [];
        foreach ($tickets as $key => $value) {
            $ticketMappers[$key] = JiraMapperCreator::createMapper();
            foreach ($ticketMappers[$key] as $mapper) {
                $mappedTickets[$key][$mapper->outputKey()] = $mapper->map($value);
            }
        }
        $this->logger->info('Tickets mapped.');
        foreach ($mappedTickets as $mappedTicket) {
            $this->dispatchJiraTicketMappedEvent($mappedTicket);
        }
    }

    /**
     * Dispatches JiraTicketMappedEvent.
     */
    private function dispatchJiraTicketMappedEvent(array $ticket) : void
    {
        $this->dispatcher->dispatch(
            JiraTicketMappedEvent::NAME,
            new JiraTicketMappedEvent($ticket)
        );
    }
}
