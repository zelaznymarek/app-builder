<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Configuration;

use AppBuilder\Application\Module\Jira\Exception\InvalidJiraStatusException;
use AppBuilder\Application\Module\Jira\ValueObject\JiraTicketStatus;

class ParametersValidator
{
    /**
     * Checks wether application params are valid.
     * Uses regex to verify hosts and a filename.
     * Returns false if any param is invalid.
     */
    public static function validate(array $paramsArray) : bool
    {
        $validJiraHost      = '/^(http:\/\/)(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*'
            . '([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])\/(jira)$/';
        $validBitbucketHost = '/^(ssh:\/\/git@)(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*'
            . '([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9]):([0-9]{4})*\/(pvg)\/$/';
        $validFilename      = '/^\/([a-zA-Z0-9])*\.(txt)$/';
        if (empty($paramsArray['parameters']['jira.host'])) {
            return false;
        }
        if (preg_match($validJiraHost, $paramsArray['parameters']['jira.host']) === 0) {
            return false;
        }

        if (preg_match($validFilename, $paramsArray['parameters']['snapshot.filename']) === 0) {
            return false;
        }

        if (preg_match($validBitbucketHost, $paramsArray['parameters']['bitbucket.repository.ssh.host']) === 0) {
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

        if (empty($paramsArray['parameters']['server.username'])) {
            return false;
        }

        if (empty($paramsArray['parameters']['snapshot.filename'])) {
            return false;
        }

        if (!is_dir($paramsArray['parameters']['server.user.project.homedir'])) {
            return false;
        }
        if (!is_dir($paramsArray['parameters']['server.vhost.dir'])) {
            return false;
        }

        if (!is_dir($paramsArray['parameters']['server.user.homedir'])) {
            return false;
        }

        $vhostPublic =
            $paramsArray['parameters']['server.vhost.dir']
            . $paramsArray['parameters']['server.username']
            . $paramsArray['parameters']['server.vhost.dir.public'];

        if (!is_dir($vhostPublic)) {
            return false;
        }

        return true;
    }
}
