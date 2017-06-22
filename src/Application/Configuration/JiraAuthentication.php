<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Configuration;

use InvalidArgumentException;

class JiraAuthentication
{
    /** @var string */
    private $host;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $host, string $username, string $password)
    {
        $this
            ->setHost($host)
            ->setUsername($username)
            ->setPassword($password);
    }

    public function host() : string
    {
        return $this->host;
    }

    public function username() : string
    {
        return $this->username;
    }

    public function password() : string
    {
        return $this->password;
    }

    public static function createFromArray(array $yamlConfigArray) : void
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setHost(string $host) : self
    {
        if (empty($host)) {
            throw new InvalidArgumentException('Empty JIRA host configuration');
        }
        $this->host = $host;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setUsername(string $username) : self
    {
        if (empty($username)) {
            throw new InvalidArgumentException('Empty JIRA username configuration');
        }
        $this->username = $username;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setPassword(string $password) : self
    {
        if (empty($password)) {
            throw new InvalidArgumentException('Empty JIRA password configuration');
        }
        $this->password = $password;

        return $this;
    }
}
