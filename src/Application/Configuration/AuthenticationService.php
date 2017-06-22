<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Configuration;

use AppBuilder\Application\Configuration\ValueObject\Parameters;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Module\Jira\QueryRepository;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use AppBuilder\Event\Application\ApplicationInitializedEvent;
use AppBuilder\Event\Application\ApplicationInitializedEventAware;
use AppBuilder\Event\Application\CredentialsValidatedEvent;
use Exception;
use FileNotFoundException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class AuthenticationService implements Authentication, ApplicationInitializedEventAware
{
    private const HTTP_OK = 200;
    /** @var QueryRepository */
    private $queryRepository;

    /** @var ExternalLibraryHttpClient */
    private $httpClient;

    /** @var FileManagerService */
    private $fileManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        QueryRepository $queryRepository,
        ExternalLibraryHttpClient $httpClient,
        FileManagerService $fileManager,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $this->queryRepository = $queryRepository;
        $this->httpClient      = $httpClient;
        $this->fileManager     = $fileManager;
        $this->logger          = $logger;
        $this->dispatcher      = $dispatcher;
    }

    public function onApplicationInitialized(ApplicationInitializedEvent $event = null) : void
    {
        if ($this->hasProperAccess()) {
            $this->dispatcher->dispatch(
                CredentialsValidatedEvent::NAME,
                new CredentialsValidatedEvent()
            );
        }
    }

    /**
     * Checks wether you have permission to access chosen ticket tracking platform.
     */
    public function validateTicketTrackingPlatform() : bool
    {
        try {
            $this->httpClient->request(
                ExternalLibraryHttpClient::GET,
                $this->createUrl($this->queryRepository->validateCredentials())
            );
        } catch (ClientException $exception) {
            $this->logger->critical('Invalid login or password');

            return false;
        } catch (ConnectException $exception) {
            $this->logger->critical('Invalid host');

            return false;
        }

        return true;
    }

    /**
     * Checks wether you have permission to access chosen git platform.
     *
     * @throws ClientException
     */
    public function validateGitPlatform() : bool
    {
        /** @var string */
        $bitbucketPath = '/bitbucket';

        try {
            $response = $this->httpClient->request(
                ExternalLibraryHttpClient::GET,
                $bitbucketPath
            );

            if (self::HTTP_OK !== $response->getStatusCode()) {
                $this->logger->warning('Failed to login Bitbucket.');

                return false;
            }
        } catch (ClientException $exception) {
            $this->logger->warning(
                sprintf(
                    'Failed to login Bitbucket. %s ',
                    $exception->getMessage()
                )
            );

            return false;
        }

        return true;
    }

    /**
     * Checks wether you have permission to access filesystem.
     */
    public function validateFilesystem() : bool
    {
        /** @var Parameters */
        $applicationParams = $this->httpClient->applicationParams();

        /** @var string */
        $fakeKey = 'fakeKey';

        /** @var string */
        $projectHomedir = $applicationParams->path($fakeKey);

        /** @var string */
        $snapshot = $applicationParams->snapshotPath('fakeKey');

        /** @var string */
        $symlinkTarget = $applicationParams->symlinkTarget($fakeKey);

        /** @var string */
        $symlinkSource = $applicationParams->symlinkSource($fakeKey);

        /** @var string */
        $fakeContent = 'content';

        try {
            return $this->createDirectories($projectHomedir, $symlinkTarget)
                && $this->createFiles($snapshot, $fakeContent, $symlinkTarget, $symlinkSource)
                && $this->removeFilesAndDirs($projectHomedir, $symlinkTarget, $snapshot);
        } catch (Exception $exception) {
            $this->logger->critical('Filesystem error: ' . $exception->getMessage());

            return false;
        }
    }

    private function hasProperAccess() : bool
    {
        return $this->validateTicketTrackingPlatform()
            && $this->validateGitPlatform()
            && $this->validateFilesystem();
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
     * Check wether user has permissions to create necessary directories.
     * Returns true if yes, false otherwise.
     */
    private function createDirectories(string $projectHomedir, string $symlinkTarget) : bool
    {
        return $this->fileManager->createDir($projectHomedir)
            && $this->fileManager->createDir($symlinkTarget);
    }

    /**
     * Check wether user has permissions to create necessary files.
     * Tries to write to and read from file.
     * Returns true if yes, false otherwise.
     *
     * @throws IOException
     * @throws FileNotFoundException
     */
    private function createFiles(
        string $snapshot,
        string $fakeContent,
        string $symlinkTarget,
        string $symlinkSource
    ) : bool {
        return $this->fileManager->filePutContent($snapshot, $fakeContent)
            && $this->fileManager->fileGetContent($snapshot) === $fakeContent
            && $this->fileManager->createSymlink($symlinkTarget, $symlinkSource);
    }

    /**
     * Check wether user has permissions to remove necessary directories and files.
     * Returns true if yes, false otherwise.
     *
     * @throws IOException
     */
    private function removeFilesAndDirs(string $projectHomedir, string $symlinkTarget, string $snapshot) : bool
    {
        $this->fileManager->remove($symlinkTarget);
        $this->fileManager->removeFile($snapshot);
        $this->fileManager->remove($projectHomedir);

        return !$this->fileManager->fileExists($snapshot)
            && !$this->fileManager->fileExists($projectHomedir);
    }
}
