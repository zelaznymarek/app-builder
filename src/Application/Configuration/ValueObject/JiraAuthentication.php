<?php

namespace PVG\Application\Configuration\ValueObject;

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
     * JiraAuthentication constructor.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     *
     * @throws InvalidArgumentException
     */
    public function __construct($host, $username, $password)
    {
        $this
            ->setHost($host)
            ->setUsername($username)
            ->setPassword($password);
    }

    /**
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this->password;
    }


    public static function createFromArray(array $yamlConfigArray)
    {

    }

    /**
     * @param string $host
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    private function setHost($host)
    {
        if (empty($host)) {
            throw new InvalidArgumentException('Empty JIRA host configuration');
        }
        $this->host = $host;

        return $this;
    }

    /**
     * @param string $username
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    private function setUsername($username)
    {
        if (empty($host)) {
            throw new InvalidArgumentException('Empty JIRA username configuration');
        }
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    private function setPassword($password)
    {
        if (empty($host)) {
            throw new InvalidArgumentException('Empty JIRA password configuration');
        }
        $this->password = $password;

        return $this;
    }


}
