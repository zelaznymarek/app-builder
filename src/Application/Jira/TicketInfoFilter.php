<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

use JiraRestApi\Issue\IssueSearchResult;
use Psr\Log\LoggerInterface;
use Pvg\Application\Jira\Exception\EmptyArrayException;
use Pvg\Event\Application\TicketsFetchedEvent;
use Pvg\Event\Application\TicketsFetchedEventAware;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TicketInfoFilter implements TicketsFetchedEventAware
{
    /** @var IssueSearchResult */
    private $tickets;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onTicketsFetched(TicketsFetchedEvent $event) : void
    {
        $this->tickets = $event->tickets();
        $this->filterTickets();
    }

    private function filterTickets() : void
    {
        $ticketsArray = null;
        foreach ($this->tickets->issues as $ticket) {
            $ticketsArray[$ticket->key] = [
                    'Ticket name'  => $ticket->key,
                    'Project name' => $ticket->fields->project->name,
                    'Summary'      => $ticket->fields->summary,
                    'Description'  => $ticket->fields->description,
                    'Status'       => $ticket->fields->status->name,
                ];
        }
        $ticketsArray = $this->convertToJSON($ticketsArray);
        $this->saveToFile($ticketsArray, 'tickets ' . date('Y-n-j', time()));
    }

    /**
     * Saves given json to file of given filename.
     */
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

    /**
     * Converts filtered IssueSearchResult object into JSON object.
     *
     * @throws EmptyArrayException
     */
    private function convertToJSON(array $result) : string
    {
        if ($result === null) {
            throw new EmptyArrayException('Is seems no tickets were fetched.');
        }
        $encoder    = [new JsonEncoder()];
        $normalizer = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizer, $encoder);

        return $serializer->serialize($result, 'json');
    }
}
