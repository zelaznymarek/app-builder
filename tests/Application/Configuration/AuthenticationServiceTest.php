<?php

declare(strict_types = 1);

namespace Tests\Application\Configuration;

use AppBuilder\Application\Configuration\AuthenticationService;
use AppBuilder\Application\Module\HttpClient\ExternalLibraryHttpClient;
use AppBuilder\Application\Module\Jira\QueryRepository;
use AppBuilder\Application\Utils\FileManager\FileManagerService;
use AppBuilder\Event\Application\CredentialsValidatedEvent;
use FileNotFoundException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @covers \AppBuilder\Application\Configuration\AuthenticationService
 */
class AuthenticationServiceTest extends TestCase
{
    /** @var QueryRepository */
    private $queryRepository;

    /** @var ExternalLibraryHttpClient | PHPUnit_Framework_MockObject_MockObject */
    private $httpClient;

    /** @var FileManagerService | PHPUnit_Framework_MockObject_MockObject */
    private $fileManager;

    /** @var LoggerInterface | PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var EventDispatcherInterface | PHPUnit_Framework_MockObject_MockObject */
    private $dispatcher;

    protected function setUp() : void
    {
        $this->queryRepository = $this->createMock(QueryRepository::class);
        $this->httpClient      = $this->createMock(ExternalLibraryHttpClient::class);
        $this->fileManager     = $this->createMock(FileManagerService::class);
        $this->logger          = $this->createMock(LoggerInterface::class);
        $this->dispatcher      = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @test
     */
    public function validateTicketTrackingPlatformWillReturnTrue() : void
    {
        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this->assertTrue($auth->validateTicketTrackingPlatform());
    }

    /**
     * @test
     */
    public function validTtpWillThrowClientException() : void
    {
        /** @var Request */
        $request = $this->createMock(Request::class);

        $this
            ->httpClient
            ->method('request')
            ->willThrowException(new ClientException('msg', $request));

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('critical')
            ->with('Invalid login or password');

        $auth->validateTicketTrackingPlatform();
    }

    /**
     * @test
     */
    public function validTtpWillThrowConnectException() : void
    {
        /** @var Request */
        $request = $this->createMock(Request::class);

        $this
            ->httpClient
            ->method('request')
            ->willThrowException(new ConnectException('msg', $request));

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('critical')
            ->with('Invalid host');

        $auth->validateTicketTrackingPlatform();
    }

    /**
     * @test
     */
    public function validateFilesystemWillReturnTrue() : void
    {
        $this->fileManager->method('createDir')->willReturn(true);
        $this->fileManager->method('filePutContent')->willReturn(true);
        $this->fileManager->method('fileGetContent')->willReturn('content');
        $this->fileManager->method('createSymlink')->willReturn(true);
        $this->fileManager->method('fileExists')->willReturn(false);

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this->assertTrue($auth->validateFilesystem());
    }

    /**
     * @test
     */
    public function validateFilesystemWillThrowFileNotFoundException() : void
    {
        $this->fileManager->method('createDir')->willReturn(true);
        $this->fileManager->method('filePutContent')->willReturn(true);
        $this->fileManager->method('fileGetContent')->willThrowException(new FileNotFoundException('msg'));

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('critical')
            ->with('Filesystem error: msg');

        $auth->validateFilesystem();
    }

    /**
     * @test
     */
    public function validateFilesystemWillThrowIOException() : void
    {
        $this->fileManager->method('createDir')->willReturn(true);
        $this->fileManager->method('filePutContent')->willThrowException(new IOException('msg'));
        $this->fileManager->method('createSymlink')->willThrowException(new IOException('msg'));
        $this->fileManager->method('remove')->willThrowException(new IOException('msg'));

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('critical')
            ->with('Filesystem error: msg');

        $auth->validateFilesystem();
    }

    /**
     * @test
     */
    public function validateGitPlatformWillReturnTrue() : void
    {
        /** @var Response */
        $response = new Response();

        $this
            ->httpClient
            ->method('request')
            ->willReturn($response);

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this->assertTrue($auth->validateGitPlatform());
    }

    /**
     * @test
     */
    public function validateGitPlatformWillFail() : void
    {
        /** @var Response */
        $response = new Response(400);

        $this
            ->httpClient
            ->method('request')
            ->willReturn($response);

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this->logger->expects($this->once())->method('warning');

        $auth->validateGitPlatform();
    }

    /**
     * @test
     */
    public function validateGitPlatformWillThrowException() : void
    {
        $this
            ->httpClient
            ->method('request')
            ->willThrowException(new ClientException('msg', new Request('method', 'uri')));

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this
            ->logger
            ->expects($this->once())
            ->method('warning')
            ->with('Failed to login Bitbucket. msg ');

        $auth->validateGitPlatform();
    }

    /**
     * @test
     */
    public function willDispatchEvent() : void
    {
        /** @var Response */
        $response = new Response();

        $this
            ->httpClient
            ->method('request')
            ->willReturn($response);

        $this->fileManager->method('createDir')->willReturn(true);
        $this->fileManager->method('filePutContent')->willReturn(true);
        $this->fileManager->method('fileGetContent')->willReturn('content');
        $this->fileManager->method('createSymlink')->willReturn(true);
        $this->fileManager->method('fileExists')->willReturn(false);

        /** @var AuthenticationService */
        $auth = new AuthenticationService(
            $this->queryRepository,
            $this->httpClient,
            $this->fileManager,
            $this->logger,
            $this->dispatcher
        );

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                CredentialsValidatedEvent::NAME,
                new CredentialsValidatedEvent()
            );

        $auth->onApplicationInitialized();
    }
}
