<?php

declare(strict_types=1);

namespace Pvg\Event\Application;

use JiraRestApi\JiraException;
use Symfony\Component\EventDispatcher\Event;

class CredentialsRejectedEvent extends Event
{
    /** @var string */
    const NAME = 'credentials.rejected';

    /** @var JiraException */
    private $exception;

    public function __construct(JiraException $exception)
    {
        $this->exception = $exception;
    }

    public function exception() : JiraException
    {
        return $this->exception;
    }
}
