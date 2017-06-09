<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\BitBucket;

use Psr\Log\LoggerInterface;
use Pvg\Application\Module\HttpClient\ExternalLibraryHttpClient;
use Pvg\Application\Utils\Mapper\Factory\BitbucketMapperFactory;
use Pvg\Event\Application\BitbucketTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEventAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryBitBucketService implements JiraTicketMappedEventAware, BitBucketService
{
    /** @var string */
    private const URL_PREFIX = 'https://farpoint.get-ag.com/jira/rest/dev-status/1.0/issue/detail?issueId=';

    /** @var string */
    private const URL_SUFFIX = '&applicationType=stash&dataType=pullrequest';

    /** @var string */
    private $url;

    /** @var LoggerInterface */
    private $logger;

    /** @var ExternalLibraryHttpClient */
    private $httpClient;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        ExternalLibraryHttpClient $httpClient,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $this->logger     = $logger;
        $this->httpClient = $httpClient;
        $this->dispatcher = $dispatcher;
    }

    /** Recives event with mapped Jira ticket. */
    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $ticketId = $event->ticket()['id'];
        $this->createUrl($ticketId);
        $this->fetchBitBucketData($ticketId);
    }

    /**
     * Fetches data from BitBucket by tickets id.
     */
    public function fetchBitBucketData(int $ticketId) : void
    {
        $bbFullTicket = [];
        $response     = $this
            ->httpClient
            ->request(ExternalLibraryHttpClient::GET, $this->url);

        $bbFullTicket[$ticketId] = json_decode($response->getBody()->getContents(), true);
        $this->mapToBitbucketTicket($bbFullTicket, $ticketId);
    }

    /**
     * Maps and filters received BitBucket data to array.
     */
    private function mapToBitbucketTicket(array $bbTicket, int $ticketId) : void
    {
        $mappedTicket  = [];
        $ticketMappers = BitbucketMapperFactory::create();
        foreach ($ticketMappers as $mapper) {
            $mappedTicket[$ticketId][$mapper->outputKey()] = $mapper->map($bbTicket[$ticketId]);
        }
        $this->dispatcher->dispatch(
            BitbucketTicketMappedEvent::NAME,
            new BitbucketTicketMappedEvent($mappedTicket)
        );
    }

    /**
     * Combines url with given ticket id.
     */
    private function createUrl(int $ticketId) : void
    {
        $this->url = self::URL_PREFIX . $ticketId . self::URL_SUFFIX;
    }
}
