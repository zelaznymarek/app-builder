<?php

declare(strict_types=1);

namespace Tests\Application\Utils\Mapper;

use PHPUnit\Framework\TestCase;
use Pvg\Application\Utils\Mapper\ArrayMapper;
use Pvg\Application\Utils\Mapper\FieldMapper;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;

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
                return (string) $data;
            }),
            new FieldMapper('email', function ($data) {
                return (string) $data;
            }),
            new FieldMapper('id', function ($data) {
                return (int) $data;
            }),
            new FieldMapper('isActive', function ($data) {
                return (bool) $data;
            }),
        ];
        $this->arrayMapper = new ArrayMapper('assignee', $mappers);
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
        $this->arrayMapper->map($data);
    }

    /**
     * @test
     * @dataProvider missingIdDataProvider
     */
    public function mapWithMissingId(array $data) : void
    {
        $this->expectException(UnsetKeyException::class);
        $this->arrayMapper->map($data);
    }

    public function correctDataProvider() : array
    {
        return [
          'data1' => [[
              'name'     => 'marek',
              'email'    => 'marek@gmail.com',
              'isActive' => true,
              'id'       => 12,
              'someKey'  => 'someValue',
          ]],
        ];
    }

    public function missingIdDataProvider() : array
    {
        return [
            'data1' => [[
                'name'     => 'marek',
                'email'    => 'marek@gmail.com',
                'isActive' => true,
                'someKey'  => 'someValue',
            ]],
        ];
    }
}
