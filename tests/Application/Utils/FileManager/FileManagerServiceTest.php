<?php

declare(strict_types = 1);

namespace Tests\Application\Utils\FileManager;

use AppBuilder\Application\Utils\FileManager\FileManagerService;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

/**
 * @covers \AppBuilder\Application\Utils\FileManager\FileManagerService
 */
class FileManagerServiceTest extends TestCase
{
    /** @var LoggerInterface | PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var string */
    private $testdir;

    /** @var string */
    private $filename;

    /** @var FileManagerService */
    private $fileManager;

    protected function setUp() : void
    {
        $this->logger      = $this->createMock(LoggerInterface::class);
        $this->fileManager = new FileManagerService($this->logger);

        $this->testdir  = __DIR__ . '/test/';
        $this->filename = 'file.txt';

        if (!is_dir($this->testdir)) {
            mkdir($this->testdir);
        }

        if (!file_exists($this->testdir . $this->filename)) {
            touch($this->testdir . $this->filename);
        }
    }

    protected function tearDown() : void
    {
        unlink($this->testdir . $this->filename);
        rmdir($this->testdir);
    }

    /**
     * @test
     */
    public function willPutFileContentSuccessfully() : void
    {
        $this->fileManager->filePutContent($this->testdir . $this->filename, 'content');

        $this->assertSame(file_get_contents($this->testdir . $this->filename), 'content');
    }

    /**
     * @test
     */
    public function putFileContentWillThrowIOException() : void
    {
        $this->expectExceptionMessage('Unable to write to the "/" directory.');

        $this->fileManager->filePutContent('/path', 'content');
    }

    /**
     * @test
     */
    public function willRemoveFileSuccessfully() : void
    {
        $this->fileManager->filePutContent(__DIR__ . '/file.txt', '');

        $this->assertTrue($this->fileManager->removeFile(__DIR__ . '/file.txt'));
    }

    /**
     * @test
     */
    public function fileGetContentSuccessful() : void
    {
        file_put_contents($this->testdir . $this->filename, 'content');

        $this->assertSame(
            'content',
            $this->fileManager->fileGetContent($this->testdir . $this->filename)
        );
    }

    /**
     * @test
     */
    public function fileExistsWillReturnTrue() : void
    {
        $this->assertTrue($this->fileManager->fileExists($this->testdir . $this->filename));
    }

    /**
     * @test
     */
    public function fileExistsWillReturnFalse() : void
    {
        $this->assertFalse($this->fileManager->fileExists($this->testdir . 'somefile.txt'));
    }

    /**
     * @test
     */
    public function changeDirWillReturnTrue() : void
    {
        $this->assertTrue($this->fileManager->changeDir($this->testdir));
    }

    /**
     * @test
     */
    public function createDirWillReturnTrue() : void
    {
        $this->assertTrue($this->fileManager->createDir(__DIR__ . '/path'));
        rmdir(__DIR__ . '/path');
    }
}
