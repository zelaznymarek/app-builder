<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Utils\FileManager;

interface FileManager
{
    /**
     * Uses Symfony Filesystem method to put data into file.
     * Creates file if it does not exist.
     */
    public function filePutContent(string $path, string $content) : bool;

    /**
     * Uses file_get_contents method to get data from file.
     */
    public function fileGetContent(string $path) : string;

    /**
     * Uses Symfony Filesystem method to remove file.
     */
    public function removeFile(string $path) : bool;

    /**
     * Checks wether passed file path is valid.
     */
    public function fileExists(string $filepath) : bool;

    /**
     * Combines filepath from given params.
     */
    public function combineFilepath(string $homedir, string $ticketKey, string $filename) : string;

    /**
     * Creates directory from passed path.
     */
    public function createDir(string $path) : bool;

    /**
     * Uses Symfony Filesystem method to cChanges working directory.
     */
    public function changeDir(string $path) : bool;

    /**
     * Uses Symfony Filesystem method to create symlinks.
     */
    public function createSymlink(string $source, string $target) : bool;
}
