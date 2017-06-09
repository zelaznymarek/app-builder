<?php

declare(strict_types = 1);

namespace Pvg\Application\Configuration\ValueObject;

use Pvg\Application\Configuration\Exception\InvalidApplicationParamsException;
use Pvg\Application\Module\Jira\Exception\InvalidJiraStatusException;
use Pvg\Application\Module\Jira\ValueObject\JiraTicketStatus;

class Parameters
{
    /** @var array */
    private $paramsArray;

    public function __construct(array $paramsArray)
    {
        if (!$this->areParamsValid($paramsArray)) {
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

    /**
     * Checks wether application params are valid.
     * Uses regex to verify hosts and a filename.
     * Returns false if any param is invalid.
     */
    private function areParamsValid(array $paramsArray) : bool
    {
        $validJiraHost      = '/^(http:\/\/)(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*'
            . '([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])\/(jira)$/';
        $validBitbucketHost = '(\w+://)(.+@)*([\w\d\.]+)(:[\d]+){0,1}([0-9]{4})(\/pvg\/)';
        $validFilename      = '/^\/([a-zA-Z0-9])*\.(txt)$/';
        if (empty($paramsArray['parameters']['jira.host'])) {
            return false;
        }
        if (preg_match($validJiraHost, $paramsArray['parameters']['jira.host']) === 0) {
            return false;
        }
        if (empty($paramsArray['parameters']['jira.authentication.username'])) {
            return false;
        }
        if (empty($paramsArray['parameters']['jira.authentication.password'])) {
            return false;
        }
        if (empty($paramsArray['parameters']['jira.watch.projects'])) {
            return false;
        }
        try {
            JiraTicketStatus::createFromString($paramsArray['parameters']['jira.trigger.deploy.state']);
            foreach ($paramsArray['parameters']['jira.trigger.remove.states'] as $status) {
                JiraTicketStatus::createFromString($status);
            }
        } catch (InvalidJiraStatusException $e) {
            return false;
        }
        if (empty($paramsArray['parameters']['bitbucket.repository.ssh.host'])) {
            return false;
        }

        if (empty($paramsArray['parameters']['jira.search.max.results'])) {
            return false;
        }

        if (preg_match($validBitbucketHost, $paramsArray['parameters']['bitbucket.repository.ssh.host']) === 0) {
            return false;
        }

        if (empty($paramsArray['parameters']['server.username'])) {
            return false;
        }
        if (!is_dir($paramsArray['parameters']['server.user.project.homedir'])) {
            return false;
        }
        if (!is_dir($paramsArray['parameters']['server.vhost.dir'])) {
            return false;
        }

        $vhostPublic =
            $paramsArray['parameters']['server.vhost.dir']
            . $paramsArray['parameters']['server.username']
            . $paramsArray['parameters']['server.vhost.dir.public'];

        if (!is_dir($vhostPublic)) {
            return false;
        }
        if (!is_dir($paramsArray['parameters']['server.user.homedir'])) {
            return false;
        }
        if (empty($paramsArray['parameters']['snapshot.filename'])) {
            return false;
        }

        if (preg_match($validFilename, $paramsArray['parameters']['snapshot.filename']) === 0) {
            return false;
        }

        return true;
    }
}
