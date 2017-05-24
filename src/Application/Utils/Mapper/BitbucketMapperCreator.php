<?php

declare(strict_types=1);

namespace Pvg\Application\Utils\Mapper;

use Closure;

class BitbucketMapperCreator
{
    /**
     * Returns a structure of mappers depending on expected result structure.
     */
    public static function createMapper() : array
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
        ];
    }
}
