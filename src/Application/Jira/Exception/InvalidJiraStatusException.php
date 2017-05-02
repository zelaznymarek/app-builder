<?php

declare(strict_types=1);

namespace Pvg\Application\Jira\Exception;

use Symfony\Component\Config\Definition\Exception\Exception;

class InvalidJiraStatusException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
