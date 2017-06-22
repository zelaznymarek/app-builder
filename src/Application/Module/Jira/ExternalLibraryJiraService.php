<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\Jira;

use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Module\Jira\Exception\InvalidJiraStatusException;
use AppBuilder\Application\Module\Jira\Exception\NullResultReturned;
use AppBuilder\Application\Module\Jira\ValueObject\JiraTicketStatus;
use AppBuilder\Application\Utils\Mapper\Factory\JiraMapperFactory;
use AppBuilder\Event\Application\CredentialsValidatedEvent;
use AppBuilder\Event\Application\CredentialsValidatedEventAware;
use AppBuilder\Event\Application\JiraTicketMappedEvent;
use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryJiraService implements JiraService, CredentialsValidatedEventAware
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
    public function onCredentialsValidated(CredentialsValidatedEvent $event = null) : void
    {
        try {
            $this->fetchAllTickets();
        } catch (Exception $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);
        }
    }

    /**
     * Fetches tickets with passed status and passes them to flattenArray method.
     *
     * @throws NullResultReturned
     * @throws RuntimeException
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

            $tickets = $this->getResponseContent($response);
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
     * @throws RuntimeException
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

        $tickets = $this->getResponseContent($response);

        if (null === $tickets) {
            throw new NullResultReturned('Error. Jira fetching method returned null');
        }

        $this->flattenArray($tickets);
    }

    /**
     * Gets content from response and decodes it into array.
     *
     * @throws RuntimeException
     */
    private function getResponseContent(Response $response) : ?array
    {
        $content = $response->getBody()->getContents();

        $this->logger->info('Tickets fetched.');

        return json_decode($content, true);
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
