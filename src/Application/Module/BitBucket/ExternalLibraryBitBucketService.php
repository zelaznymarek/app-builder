<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Module\BitBucket;

use AppBuilder\Application\Module\BitBucket\Exception\NullResultReturned;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Utils\Mapper\Factory\BitbucketMapperFactory;
use AppBuilder\Event\Application\BitbucketTicketMappedEvent;
use AppBuilder\Event\Application\JiraTicketMappedEvent;
use AppBuilder\Event\Application\JiraTicketMappedEventAware;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use RuntimeException;
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
        try {
            $this->fetchBitBucketData($ticketId);
        } catch (RuntimeException $exception) {
            $this->logger->warning('Error: ' . $exception->getMessage(), [$exception]);
        }
    }

    /**
     * Fetches data from BitBucket by tickets id.
     *
     * @throws RuntimeException
     * @throws NullResultReturned
     */
    public function fetchBitBucketData(int $ticketId) : void
    {
        $bbFullTicket = [];
        $response     = $this
            ->httpClient
            ->request(ExternalLibraryHttpClient::GET, $this->url);
        $bbFullTicket[$ticketId] = $this->getResponseContent($response);

        if (null === $bbFullTicket[$ticketId]) {
            throw new NullResultReturned('Error. Bitbucket fetching method returned null');
        }

        $this->mapToBitbucketTicket($bbFullTicket, $ticketId);
    }

    /**
     * Gets content from response and decodes it into array.
     *
     * @throws RuntimeException
     */
    private function getResponseContent(Response $response) : ?array
    {
        $content = $response->getBody()->getContents();

        return json_decode($content, true);
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
