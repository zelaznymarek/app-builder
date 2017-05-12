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
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class ExternalLibraryJiraService implements
    JiraService,
    ApplicationInitializedEventAware
{
    /** @var int */
    private const MAX_RESULTS = 100;

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
            $this->dispatchTicketsFetchedEvent(
                $this->mapToJiraTicket(
                    $this->issueSearchResultToArray($tickets)
                )
            );
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
        $ticketsArray  = $this->issueSearchResultToArray($tickets);
        $mappedTickets = $this->mapToJiraTicket($ticketsArray);
        $this->dispatchTicketsFetchedEvent($mappedTickets);
        $this->saveToFile(json_encode($mappedTickets), 'mappedTickets');
    }

    /**
     * Maps IssueSearchResult to JiraTicket object.
     * This method probably will be moved to separate class.
     */
    public function mapToJiraTicket(array $tickets) : array
    {
        $mappedTickets = [];
        $jiraTickets   = [];
        foreach ($tickets as $key => $value) {
            $jiraTickets[$key] = JiraMapperCreator::createMapper();
            foreach ($jiraTickets[$key] as $mapper) {
                $mappedTickets[$key][$mapper->outputKey()] = $mapper->map($value);
            }
        }

        return $mappedTickets;
    }

    private function issueSearchResultToArray(IssueSearchResult $isr) : array
    {
        $ticketsArray = [];
        foreach ($isr->issues as $issue) {
            $ticketsArray[$issue->key] = json_decode(json_encode($issue), true);
        }

        return $ticketsArray;
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
    private function dispatchTicketsFetchedEvent(array $tickets) : void
    {
        $this->dispatcher->dispatch(
            TicketsFetchedEvent::NAME,
            new TicketsFetchedEvent($tickets)
        );
    }

    /*************** FOR EASIER DEVELOPMENT ONLY, PLEASE DON'T HATE IT ***************/

    private function saveToFile(string $result, string $filename) : void
    {
        if ($result !== null) {
            $fs = new Filesystem();
            try {
                $fs->dumpFile("/home/maro/$filename.json", $result);
            } catch (IOException $e) {
                $this->logger->info("Could not write to file\n" . $e->getMessage());
            }
        } else {
            $this->logger->info('Nothing fetched');
        }
    }
}
