<?php

declare(strict_types=1);

namespace Pvg\Application\Module\BitBucket;

use Psr\Log\LoggerInterface;
use Pvg\Application\Utils\Mapper\BitbucketMapperCreator;
use Pvg\Event\Application\BitbucketTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEvent;
use Pvg\Event\Application\JiraTicketMappedEventAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExternalLibraryBitBucketService implements JiraTicketMappedEventAware
{
    /** @var string */
    private const URL_PREFIX = 'https://farpoint.get-ag.com/jira/rest/dev-status/1.0/issue/detail?issueId=';

    /** @var string */
    private const URL_SUFFIX = '&applicationType=stash&dataType=pullrequest';

    /** @var string */
    private $url;

    /** @var int */
    private $ticketId;

    /** @var LoggerInterface */
    private $logger;

    /** @var GuzzleClient */
    private $guzzleClient;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        GuzzleClient $guzzleClient,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $this->logger       = $logger;
        $this->guzzleClient = $guzzleClient;
        $this->dispatcher   = $dispatcher;
    }

    /** Recives event with mapped Jira ticket. */
    public function onJiraTicketMapped(JiraTicketMappedEvent $event) : void
    {
        $this->ticketId = $event->ticket()['id'];
        $this->createUrl();
        $this->fetchBitBucketData();
    }

    /**
     * Fetches data from BitBucket by tickets id.
     */
    private function fetchBitBucketData() : void
    {
        $response = $this
            ->guzzleClient
            ->client()
            ->request('GET', $this->url, [
                'auth'    => [$this->guzzleClient->user(), $this->guzzleClient->password()],
                'headers' => ['Accept' => 'application/json'],
            ]);

        $BBfullTicket[$this->ticketId] = json_decode($response->getBody(), true);
        $this->mapToBitbucketTicket($BBfullTicket);
    }

    /**
     * Maps and filters recived BitBucket data to array.
     */
    private function mapToBitbucketTicket(array $bbTicket) : void
    {
        $mappedTicket  = [];
        $ticketMappers = BitbucketMapperCreator::createMapper();
        foreach ($ticketMappers as $mapper) {
            $mappedTicket[$this->ticketId][$mapper->outputKey()] = $mapper->map($bbTicket[$this->ticketId]);
        }
        $this->dispatcher->dispatch(BitbucketTicketMappedEvent::NAME,
            new BitbucketTicketMappedEvent($mappedTicket));
    }

    /**
     * Combines url with given ticket id.
     */
    private function createUrl() : void
    {
        $this->url = self::URL_PREFIX . $this->ticketId . self::URL_SUFFIX;
    }
}
