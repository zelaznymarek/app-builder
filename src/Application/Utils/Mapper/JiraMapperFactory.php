<?php

declare(strict_types=1);

namespace Pvg\Application\Utils\Mapper;

use Closure;

class JiraMapperFactory
{
    /**
     * Returns a structure of mappers depending on expected result structure.
     */
    public static function create() : array
    {
        /** @var Closure */
        $intClosure = function (string $data) : int {
            return (int) $data;
        };

        /** @var Closure */
        $stringClosure = function (string $data) : string {
            return $data;
        };

        /** @var Closure */
        $boolClosure = function (?bool $data) : bool {
            return $data;
        };

        return [
            new FieldMapper('id', $intClosure),
            new FieldMapper('key', $stringClosure, 'ticket_key'),

            /* Assignee name */
            new ArrayMapper('fields', [
                new ArrayMapper('assignee', [
                    new FieldMapper('name', $stringClosure),
                ]),
            ], 'assignee_name'),

            /* Assignee email */
            new ArrayMapper('fields', [
                new ArrayMapper('assignee', [
                    new FieldMapper('emailAddress', $stringClosure, 'email'),
                ]),
            ], 'assignee_email'),

            /* Assignee display name */
            new ArrayMapper('fields', [
                new ArrayMapper('assignee', [
                    new FieldMapper('displayName', $stringClosure, 'display_name'),
                ]),
            ], 'assignee_display_name'),

            /* Assignee active */
            new ArrayMapper('fields', [
                new ArrayMapper('assignee', [
                    new FieldMapper('active', $boolClosure),
                ]),
            ], 'assignee_active'),

            /* Status */
            new ArrayMapper('fields', [
                new ArrayMapper('status', [
                    new FieldMapper('name', $stringClosure, 'status'),
                ]),
            ], 'status'),

            /* Status category */
            new ArrayMapper('fields', [
                new ArrayMapper('status', [
                    new ArrayMapper('statuscategory', [
                        new FieldMapper('name', $stringClosure, 'status_category'),
                    ]),
                ]),
            ], 'status_category'),

            /* Components */
            new ArrayMapper('fields', [
                new ArrayMapper('components', [
                    new ArrayMapper('0', [
                        new FieldMapper('name', $stringClosure),
                    ]),
                ]),
            ], 'components'),

            /* Issue type */
            new ArrayMapper('fields', [
                new ArrayMapper('issuetype', [
                    new FieldMapper('name', $stringClosure, 'ticket_type'),
                ]),
            ], 'ticket_type'),

            /* Project */
            new ArrayMapper('fields', [
                new ArrayMapper('project', [
                    new FieldMapper('name', $stringClosure, 'project'),
                ]),
            ], 'project'),

            /* Fix version */
            new ArrayMapper('fields', [
                new ArrayMapper('fixVersions', [
                    new ArrayMapper('0', [
                        new FieldMapper('name', $stringClosure, 'fix_version'),
                    ]),
                ]),
            ], 'fix_version'),

            /* Summary */
            new ArrayMapper('fields', [
                new FieldMapper('summary', $stringClosure),
            ], 'summary'),
        ];
    }
}
