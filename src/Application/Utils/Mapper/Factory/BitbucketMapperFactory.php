<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Utils\Mapper\Factory;

use AppBuilder\Application\Utils\Mapper\ArrayMapper;
use AppBuilder\Application\Utils\Mapper\FieldMapper;
use Closure;

class BitbucketMapperFactory
{
    /**
     * Returns a structure of mappers depending on expected result structure.
     */
    public static function create() : array
    {
        /** @var Closure */
        $stringClosure = function (string $data) : string {
            return $data;
        };

        return [
            /* Name */
            new ArrayMapper('detail', [
                new ArrayMapper('0', [
                    new ArrayMapper('pullRequests', [
                        new ArrayMapper('0', [
                            new FieldMapper('name', $stringClosure),
                        ]),
                    ]),
                ]),
            ], 'pull_request_name'),

            /* Url */
            new ArrayMapper('detail', [
                new ArrayMapper('0', [
                    new ArrayMapper('pullRequests', [
                        new ArrayMapper('0', [
                            new FieldMapper('url', $stringClosure),
                        ]),
                    ]),
                ]),
            ], 'pull_request_url'),

            /* Status */
            new ArrayMapper('detail', [
                new ArrayMapper('0', [
                    new ArrayMapper('pullRequests', [
                        new ArrayMapper('0', [
                            new FieldMapper('status', $stringClosure),
                        ]),
                    ]),
                ]),
            ], 'pull_request_status'),

            /* Last update */
            new ArrayMapper('detail', [
                new ArrayMapper('0', [
                    new ArrayMapper('pullRequests', [
                        new ArrayMapper('0', [
                            new FieldMapper('lastUpdate', $stringClosure),
                        ]),
                    ]),
                ]),
            ], 'pull_request_last_update'),

            /* Branch */
            new ArrayMapper('detail', [
                new ArrayMapper('0', [
                    new ArrayMapper('pullRequests', [
                        new ArrayMapper('0', [
                            new ArrayMapper('source', [
                                new FieldMapper('branch', $stringClosure),
                            ]),
                        ]),
                    ]),
                ]),
            ], 'pull_request_branch'),

            /* Repository */
            new ArrayMapper('detail', [
                new ArrayMapper('0', [
                    new ArrayMapper('pullRequests', [
                        new ArrayMapper('0', [
                            new ArrayMapper('source', [
                                new ArrayMapper('repository', [
                                    new FieldMapper('name', $stringClosure),
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ], 'repository'),
        ];
    }
}
