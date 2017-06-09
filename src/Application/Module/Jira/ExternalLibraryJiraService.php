<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\Jira;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Log\LoggerInterface;
use Pvg\Application\Module\HttpClient\ExternalLibraryHttpClient;
use Pvg\Application\Module\Jira\Exception\InvalidJiraStatusException;
use Pvg\Application\Module\Jira\Exception\NullResultReturned;
use Pvg\Application\Module\Jira\ValueObject\JiraTicketStatus;
use Pvg\Application\Utils\Mapper\Factory\JiraMapperFactory;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Pvg\Event\Application\ApplicationInitializedEventAware;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryJiraService implements JiraService, ApplicationInitializedEventAware
{
    /** @var ExternalLibraryHttpClient */
    private $httpClient;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var QueryRepository */
    private $queryRepository;

    public function __construct(
        ExternalLibraryHttpClient $httpClient,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
        QueryRepository $queryRepository
    ) {
        $this->httpClient      = $httpClient;
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
        if ($this->validateCredentials()) {
            try {
                $this->fetchAllTickets();
            } catch (NullResultReturned $exception) {
                $this->logger->warning($exception->getMessage(), [$exception]);
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
            $this->httpClient->request(
                ExternalLibraryHttpClient::GET,
                $this->createUrl($this->queryRepository->validateCredentials())
            );

            return true;
        } catch (ClientException $exception) {
            $this->logger->warning('Invalid login or password');

            return false;
        } catch (ConnectException $exception) {
            $this->logger->warning('Invalid host');

            return false;
        }
    }

    /**
     * Fetches tickets with passed status and passes them to flattenArray method.
     *
     * @throws NullResultReturned
     */
    public function fetchTicketsByStatus(string $status) : void
    {
        $tickets = null;
        try {
            $response = $this
                ->httpClient->request(
                    ExternalLibraryHttpClient::GET,
                    $this
                        ->createUrl($this
                            ->queryRepository
                            ->fetchTicketsByStatus(JiraTicketStatus::createFromString($status)->status()))
                );
            $this->logger->info('Tickets fetched.');

            $tickets = json_decode($response->getBody()->getContents(), true);
        } catch (InvalidJiraStatusException $exception) {
            $this->logger->warning('Error: ' . $exception->getMessage(), [$exception]);
        }

        if (null === $tickets) {
            throw new NullResultReturned('Error. Fetching method returned null');
        }
        $this->flattenArray($tickets);
    }

    /**
     * Fetches all tickets and passes them to flattenArray method.
     *
     * @throws NullResultReturned
     */
    public function fetchAllTickets() : void
    {
        $response = $this
            ->httpClient->request(
                ExternalLibraryHttpClient::GET,
                $this
                    ->createUrl($this
                        ->queryRepository
                        ->fetchAllTickets())
            );
        $tickets = json_decode($response->getBody()->getContents(), true);

        $this->logger->info('Tickets fetched.');
        if (null === $tickets) {
            throw new NullResultReturned('Error. Fetching method returned null');
        }
        $this->flattenArray($tickets);
    }

    /**
     * Reconstructs array structure.
     */
    private function flattenArray(array $tickets) : void
    {
        $ticketsArray = [];
        foreach ($tickets['issues'] as $issue) {
            $ticketsArray[$issue['key']] = $issue;
        }

        $this->mapToJiraTicket($ticketsArray);
    }

    /**
     * Combines jira rest api url with jql.
     */
    private function createUrl(string $jql) : string
    {
        return $this->httpClient->applicationParams()->jiraHost()
            . '/rest/api/2/search?jql='
            . $jql
            . '&maxResults='
            . $this->httpClient->applicationParams()->jiraSearchMaxResults();
    }

    /**
     * Maps and filters array with full Jira ticket into desired array format.
     */
    private function mapToJiraTicket(array $tickets) : void
    {
        $mappedTickets = [];
        $ticketMappers = [];
        foreach ($tickets as $key => $value) {
            $ticketMappers[$key] = JiraMapperFactory::create();
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
