<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Configuration;

interface Authentication
{
    /**
     * Checks wether you have permission to access chosen ticket tracking platform.
     */
    public function validateTicketTrackingPlatform() : bool;

    /**
     * Checks wether you have permission to access chosen git platform.
     */
    public function validateGitPlatform() : bool;

    /**
     * Checks wether you have permission to access filesystem.
     */
    public function validateFilesystem() : bool;
}
