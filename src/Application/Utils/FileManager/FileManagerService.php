<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Utils\FileManager;

use FileNotFoundException;
use Nette\DirectoryNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileManagerService extends Filesystem implements FileManager
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Uses Symfony Filesystem method to put data into file.
     * Creates file if it does not exist.
     *
     * @throws IOException
     */
    public function filePutContent(string $path, string $content) : bool
    {
        $this->dumpFile($path, $content);

        return true;
    }

    /**
     * Uses file_get_contents method to get data from file.
     *
     * @throws FileNotFoundException
     */
    public function fileGetContent(string $path) : string
    {
        $content = file_get_contents($path);
        if (null === $content) {
            throw new FileNotFoundException('Could not get content from file ' . $path);
        }

        return $content;
    }

    /**
     * Uses Symfony Filesystem method to remove file.
     */
    public function removeFile(string $path) : bool
    {
        try {
            $this->remove($path);
        } catch (IOException $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);
        }

        return true;
    }

    /**
     * Combines filepath from given params.
     */
    public function combineFilepath(string $homedir, string $ticketKey, string $filename) : string
    {
        return $homedir . $ticketKey . $filename;
    }

    /**
     * Checks wether passed file path is valid.
     */
    public function fileExists(string $path) : bool
    {
        return $this->exists($path);
    }

    /**
     * Creates directory from passed path.
     */
    public function createDir(string $path) : bool
    {
        try {
            $this->mkdir($path);
        } catch (IOException $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);
        }

        return true;
    }

    /**
     * Changes working directory to passed in param.
     *
     * @throws DirectoryNotFoundException
     */
    public function changeDir(string $path) : bool
    {
        if (!chdir($path)) {
            throw new DirectoryNotFoundException('Could not enter ' . $path);
        }

        return true;
    }

    /**
     * Uses Symfony Filesystem method to create symlinks.
     *
     * @throws IOException
     */
    public function createSymlink(string $source, string $target) : bool
    {
        $this->symlink($source, $target);

        return true;
    }
}
