<?php


namespace Pvg\Application\Jira\Exception;


use Symfony\Component\Config\Definition\Exception\Exception;
use Throwable;

class EmptyArrayException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}