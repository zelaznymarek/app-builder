<?php

declare(strict_types = 1);

namespace Tests\Application\Utils\Mapper;

use Closure;
use PHPUnit\Framework\TestCase;
use Pvg\Application\Module\Jira\Exception\MapperRepetitionException;
use Pvg\Application\Utils\Mapper\ArrayMapper;
use Pvg\Application\Utils\Mapper\FieldMapper;

/**
 * @covers \Pvg\Application\Utils\Mapper\ArrayMapper
 */
class ArrayMapperTest extends TestCase
{
    /** @var array */
    private $mappers = [];

    /** @var array */
    private $recurringKeyMappers = [];

    /** @var array */
    private $mappedTickets;

    public function setUp() : void
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
        $boolClosure = function (bool $data) : bool {
            return $data;
        };

        $this->recurringKeyMappers = [
            new FieldMapper('summary', $stringClosure),
            new FieldMapper('summary', $stringClosure),
        ];

        $this->mappers = [
            new FieldMapper('id', $intClosure),
            new FieldMapper('key', $stringClosure, 'ticket_key'),
            new ArrayMapper('fields', [
                new ArrayMapper('assignee', [
                    new FieldMapper('name', $stringClosure),
                ]),
            ], 'assignee_name'),
            new ArrayMapper('fields', [
                new ArrayMapper('assignee', [
                    new FieldMapper('active', $boolClosure),
                ]),
            ], 'assignee_active'),

            new ArrayMapper('fields', [
                new ArrayMapper('status', [
                    new FieldMapper('name', $stringClosure),
                ]),
            ], 'status'),

            new ArrayMapper('fields', [
                new ArrayMapper('status', [
                    new FieldMapper('status_category', $stringClosure),
                ]),
            ], 'status_category'),
        ];
    }

    /**
     * @test
     * @dataProvider correctDataProvider
     */
    public function mapWithCorrectMapper(array $data) : void
    {
        foreach ($this->mappers as $mapper) {
            $this->mappedTickets['IN-4'][$mapper->outputKey()] = $mapper->map($data);
        }

        $this->assertSame(12, $this->mappedTickets['IN-4']['id']);
        $this->assertSame('IN-4', $this->mappedTickets['IN-4']['ticket_key']);
        $this->assertSame('marek', $this->mappedTickets['IN-4']['assignee_name']);
        $this->assertTrue($this->mappedTickets['IN-4']['assignee_active']);
        $this->assertSame('Done', $this->mappedTickets['IN-4']['status']);
        $this->assertSame('Done...', $this->mappedTickets['IN-4']['status_category']);
    }

    /**
     * @test
     */
    public function mapWithIncorrectMapper() : void
    {
        $this->expectException(MapperRepetitionException::class);
        new ArrayMapper('ticket', $this->recurringKeyMappers);
    }

    public function correctDataProvider() : array
    {
        return [
            'data1' => [
                    'IN-4' => [
                        'id'     => '12',
                        'key'    => 'IN-4',
                        'fields' => [
                            'assignee' => [
                                'name'   => 'marek',
                                'active' => true,
                            ],
                            'status' => [
                                'name'            => 'Done',
                                'status_category' => 'Done...',
                            ],
                        ],
                    ],
                ],
        ];
    }
}
