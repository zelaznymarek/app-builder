<?php

declare(strict_types = 1);

namespace Pvg\Application\Module\TaskManager\Exception;

use Symfony\Component\Config\Definition\Exception\Exception;
use Throwable;

class MisguidedTaskException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Task ' . $message . ' does not match', $code, $previous);
    }
}
