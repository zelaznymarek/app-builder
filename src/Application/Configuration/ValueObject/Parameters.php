<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Configuration\ValueObject;

use AppBuilder\Application\Configuration\Exception\InvalidApplicationParamsException;
use AppBuilder\Application\Configuration\ParametersValidator;

class Parameters
{
    /** @var array */
    private $paramsArray;

    public function __construct(array $paramsArray)
    {
        if (!ParametersValidator::validate($paramsArray)) {
            throw new InvalidApplicationParamsException('Invalid application parameters');
        }
        $this->paramsArray = $paramsArray;
    }

    /**
     * Returns jira host.
     */
    public function jiraHost() : string
    {
        return $this->paramsArray['parameters']['jira.host'];
    }

    /**
     * Returns jira auth user.
     */
    public function authenticationUser() : string
    {
        return $this->paramsArray['parameters']['jira.authentication.username'];
    }

    /**
     * Returns jira auth password.
     */
    public function authenticationPassword() : string
    {
        return $this->paramsArray['parameters']['jira.authentication.password'];
    }

    /**
     * Returns jira projects to watch.
     */
    public function jiraWatchProjects() : array
    {
        return $this->paramsArray['parameters']['jira.watch.projects'];
    }

    /**
     * Returns jira deploy status.
     */
    public function jiraDeployStatus() : string
    {
        return $this->paramsArray['parameters']['jira.trigger.deploy.state'];
    }

    /**
     * Returns jira remove status.
     */
    public function jiraRemoveStatuses() : array
    {
        return $this->paramsArray['parameters']['jira.trigger.remove.states'];
    }

    /**
     * Returns jira search max results
     */
    public function jiraSearchMaxResults() : int
    {
        return $this->paramsArray['parameters']['jira.search.max.results'];
    }

    /**
     * Returns bitbucket repository ssh host.
     */
    public function bitbucketRepositoryHost() : string
    {
        return $this->paramsArray['parameters']['bitbucket.repository.ssh.host'];
    }

    /**
     * Returns username.
     */
    public function username() : string
    {
        return $this->paramsArray['parameters']['server.username'];
    }

    /**
     * Returns path to test instances directory.
     */
    public function projectsHomeDir() : string
    {
        return $this->paramsArray['parameters']['server.user.project.homedir'];
    }

    /**
     * Returns server host directory.
     */
    public function serverHostDir() : string
    {
        return $this->paramsArray['parameters']['server.vhost.dir'];
    }

    /**
     * Returns server host public directory.
     */
    public function serverPublicHostDir() : string
    {
        return $this->paramsArray['parameters']['server.vhost.dir.public'];
    }

    /**
     * Returns home directory.
     */
    public function homeDirectory() : string
    {
        return $this->paramsArray['parameters']['server.user.homedir'];
    }

    /**
     * Returns snapshot file name.
     */
    public function snapshotFileName() : string
    {
        return $this->paramsArray['parameters']['snapshot.filename'];
    }

    /**
     * Combines path to particular test instance.
     */
    public function path(string $ticketKey) : string
    {
        return $this->projectsHomeDir() . $ticketKey;
    }

    /**
     * Combines filepath to particular snapshot.
     */
    public function snapshotPath(string $ticketKey) : string
    {
        return $this->path($ticketKey) . $this->snapshotFileName();
    }

    /**
     * Combines symlink target.
     */
    public function symlinkTarget(string $ticketKey) : string
    {
        return $this->projectsHomeDir() . $ticketKey . $this->serverPublicHostDir();
    }

    /**
     * Combines symlink source.
     */
    public function symlinkSource(string $ticketKey) : string
    {
        return $this->serverHostDir() . $this->username() . $this->serverPublicHostDir() . $ticketKey;
    }
}
