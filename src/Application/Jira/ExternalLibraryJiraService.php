<?php

declare(strict_types=1);

namespace Pvg\Application\Jira;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;
use Pvg\Event\Application\ApplicationInitializedEvent;
use Pvg\Event\Application\ApplicationInitializedEventAware;
use Pvg\Event\Application\CredentialsRejectedEvent;
use Pvg\Event\Application\CredentialsRejectedEventAware;
use Pvg\Event\Application\CredentialsValidatedEvent;
use Pvg\Event\Application\CredentialsValidatedEventAware;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ExternalLibraryJiraService implements
    JiraService,
    ApplicationInitializedEventAware,
    CredentialsValidatedEventAware,
    CredentialsRejectedEventAware
{
    /** @var IssueService */
    private $jiraService;

    /** @var LoggerInterface */
    private $logger;

    /** @var ArrayConfiguration */
    private $configArray;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var array */
    private $availableStatuses;

    public function __construct(
        array $applicationConfig,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $this->configArray = new ArrayConfiguration([
                'jiraHost'     => $applicationConfig['parameters']['jira.host'],
                'jiraUser'     => $applicationConfig['parameters']['jira.authentication.username'],
                'jiraPassword' => $applicationConfig['parameters']['jira.authentication.password'],
            ]);
        $this->jiraService       = new IssueService($this->configArray);
        $this->logger            = $logger;
        $this->dispatcher        = $dispatcher;
        $this->availableStatuses = [
            'Abgelehnt'         => 'Abgelehnt',
            'Approved'          => 'Approved',
            'Backlog'           => 'Backlog',
            'Cancelled'         => 'Cancelled',
            'Check finished'    => 'Check finished',
            'Check in progress' => 'Check in progress',
            'Check waiting'     => 'Check waiting',
            'Closed'            => 'Closed',
            'Done'              => 'Done',
            'Erledigt'          => 'Erledigt',
            'Gelöst'            => 'Gelöst',
            'In Progress'       => 'In Progress',
            'In Review'         => 'In Review',
            'Open'              => 'Open',
            'Rejected'          => 'Rejected',
        ];
    }

    /**
     * Method uses provided credentials to connect JIRA and get information about user.
     * If succesfull it dispatches an event and returns true. In case credentials are invalid
     * it catches JiraException, dispatches an event and returns false.
     */
    public function login() : bool
    {
        $jql = 'assignee  = "marek.zelazny@equiqo.com" ';
        try {
            $this->jiraService->search($jql);
            $this->dispatcher->dispatch(
                CredentialsValidatedEvent::NAME,
                new CredentialsValidatedEvent()
            );

            return true;
        } catch (JiraException $e) {
            $this->dispatcher->dispatch(
                CredentialsRejectedEvent::NAME,
                new CredentialsRejectedEvent($e)
            );

            return false;
        }
    }

    /**
     * Service connects to JIRA with specified credentials when event occurs.
     */
    public function onApplicationInitialized(ApplicationInitializedEvent $event) : void
    {
        $this->login();
    }

    /**
     * Gets tickets with given status when event occurs.
     */
    public function onCredentialsValidated(CredentialsValidatedEvent $event) : void
    {
        $this->logger->info('Logged to JIRA');
        $this->getAllIssues([$this->availableStatuses['In Progress']]);
    }

    /**
     * Logs error message when event occurs.
     */
    public function onCredentialsRejected(CredentialsRejectedEvent $event) : void
    {
        $this->logger->info('Login failed \n' . $event->exception()->getMessage());
    }

    /**
     * Fetches tickets with passed status, converts into JSON and saves to file.
     */
    private function getAllIssues(array $statusArray) : void
    {
        foreach ($statusArray as $status) {
            $jql    = 'status = "' . $status . '"';
            $issues = $this->sendQuery($jql);
            $issues = $this->convertToJSON($issues);
            $this->saveToFile($issues, $status);
        }
    }

    /**
     * Sends passed JQL query to JIRA and returns its result.
     */
    private function sendQuery(string $jql) : IssueSearchResult
    {
        $result = null;
        try {
            $result = $this->jiraService->search($jql, 0, 1000);
        } catch (JiraException $e) {
            $this->logger->info('Search Failed : ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Saves given json to file of given filename.
     */
    private function saveToFile(string $result, string $filename) : void
    {
        if ($result !== null) {
            try {
                $fp = fopen("$filename.json", 'w');
                fwrite($fp, $result);
                fclose($fp);
            } catch (Exception $e) {
                $this->logger->info("Could not write to file\n" . $e->getMessage());
            }
        } else {
            $this->logger->info('Nothing fetched');
        }
    }

    /**
     * Converts IssueSearchResult object into JSON object.
     */
    private function convertToJSON(IssueSearchResult $result) : string
    {
        $encoder    = [new JsonEncoder()];
        $normalizer = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizer, $encoder);

        return $serializer->serialize($result, 'json');
    }
}
