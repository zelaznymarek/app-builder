<?php

declare(strict_types = 1);

namespace Tests\Application\Configuration\ValueObject;

use AppBuilder\Application\Configuration\Exception\InvalidApplicationParamsException;
use AppBuilder\Application\Configuration\ValueObject\Parameters;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AppBuilder\Application\Configuration\ValueObject\Parameters
 */
class ParametersTest extends TestCase
{
    protected function setUp() : void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('virtualDir'));
    }

    /**
     * @test
     * @dataProvider invalidParams
     */
    public function willThrowInvalidApplicationParamsException(array $params) : void
    {
        $this->expectException(InvalidApplicationParamsException::class);
        new Parameters($params);
    }

    public function invalidParams() : array
    {
        return [
            'emptyHost' => [
                [
                    'parameters' => [
                        'jira.host'                     => '',
                        'jira.authentication.username'  => 'username',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyAuthUser' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => '',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyAuthPass' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => '',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyWatchProjects' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => [],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyDeployState' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => '',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyRemoteStates' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => [],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyMaxResults' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyBBssh' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => '',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyUsername' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => '',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyProjHomeDir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '',
                        'server.vhost.dir'              => '/var/dir',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyVhostDir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => '',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyVhostDirPublic' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => vfsStream::url('virtualDir'),
                        'server.vhost.dir.public'       => '',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyHomedir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => vfsStream::url('virtualDir'),
                        'server.vhost.dir.public'       => vfsStream::url('virtualDir'),
                        'server.user.homedir'           => '',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'emptyFilename' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => vfsStream::url('virtualDir'),
                        'server.vhost.dir.public'       => vfsStream::url('virtualDir'),
                        'server.user.homedir'           => vfsStream::url('virtualDir'),
                        'snapshot.filename'             => '',
                    ],
                ],
            ],
            'invalidProjectHomeDir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => '/dir',
                        'server.vhost.dir'              => 'var',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'invalidVhostDir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => 'user',
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => 'var',
                        'server.vhost.dir.public'       => '/httpdocs/',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'invalidVhostPublicDir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => vfsStream::url('virtualDir'),
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => vfsStream::url('virtualDir'),
                        'server.vhost.dir.public'       => 'dir',
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'invalidHomedir' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => vfsStream::url('virtualDir'),
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => vfsStream::url('virtualDir'),
                        'server.vhost.dir.public'       => vfsStream::url('virtualDir'),
                        'server.user.homedir'           => '/home/user/',
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'invalidVhostPublic' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'jira.authentication.username'  => 'user',
                        'jira.authentication.password'  => 'pass',
                        'jira.watch.projects'           => ['pvg'],
                        'jira.trigger.deploy.state'     => 'Work in progress',
                        'jira.trigger.remove.states'    => ['Done'],
                        'jira.search.max.results'       => [100],
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                        'server.username'               => vfsStream::url('virtualDir'),
                        'server.user.project.homedir'   => vfsStream::url('virtualDir'),
                        'server.vhost.dir'              => vfsStream::url('virtualDir'),
                        'server.vhost.dir.public'       => vfsStream::url('virtualDir'),
                        'server.user.homedir'           => vfsStream::url('virtualDir'),
                        'snapshot.filename'             => '/file.txt',
                    ],
                ],
            ],
            'invalidJiraHost' => [
                [
                    'parameters' => [
                        'jira.host' => 'host',
                    ],
                ],
            ],
            'invalidFilename' => [
                [
                    'parameters' => [
                        'jira.host'         => 'http://host.domain.com/jira',
                        'snapshot.filename' => 'file',
                    ],
                ],
            ],
            'invalidBBHost' => [
                [
                    'parameters' => [
                        'jira.host'                     => 'http://host.domain.com/jira',
                        'snapshot.filename'             => 'file',
                        'bitbucket.repository.ssh.host' => 'ssh://git@host.domain.com:0000/pvg/',
                    ],
                ],
            ],
        ];
    }
}
