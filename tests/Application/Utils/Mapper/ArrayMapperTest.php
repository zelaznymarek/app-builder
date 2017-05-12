<?php

declare(strict_types=1);

namespace Tests\Application\Utils\Mapper;

use PHPUnit\Framework\TestCase;
use Pvg\Application\Utils\Mapper\ArrayMapper;
use Pvg\Application\Utils\Mapper\FieldMapper;

/**
 * @coversNothing
 */
class ArrayMapperTest extends TestCase
{
    /** @var ArrayMapper */
    private $arrayMapper;

    public function setUp() : void
    {
        $mappers = [
            new FieldMapper('name', function ($data) {
                return $data;
            }),
            new FieldMapper('email', function ($data) {
                return $data;
            }),
            new FieldMapper('id', function ($data) {
                return (int) $data;
            }),
            new FieldMapper('isActive', function ($data) {
                return $data;
            }),
            new FieldMapper('displayName', function ($data) {
                return $data;
            }, 'display_name'),
        ];
        $this->arrayMapper = new ArrayMapper('IN-4', $mappers);
    }

    /**
     * @test
     * @dataProvider correctDataProvider
     */
    public function mapWithCorrectData(array $data) : void
    {
        $this->assertTrue($this->arrayMapper->map($data)['isActive']);
        $this->assertSame(12, $this->arrayMapper->map($data)['id']);
        $this->assertSame('marek', $this->arrayMapper->map($data)['name']);
        $this->assertSame('marek@gmail.com', $this->arrayMapper->map($data)['email']);
        $this->assertSame('', $this->arrayMapper->map($data)['display_name']);

        $this->arrayMapper->map($data);
    }

    public function correctDataProvider() : array
    {
        return [
            'data1' => [
                'array' => [
                    'IN-4' => [
                        'name'        => 'marek',
                        'id'          => 12,
                        'email'       => 'marek@gmail.com',
                        'isActive'    => true,
                        'displayName' => '',
                    ],
                ],
            ],
        ];
    }
}
