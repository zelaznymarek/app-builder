<?php

declare(strict_types=1);

namespace Tests\Application\Utils\Mapper;

use Closure;
use PHPUnit\Framework\TestCase;
use Pvg\Application\Utils\Mapper\FieldMapper;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;

/**
 * @coversNothing
 */
class FieldMapperTest extends TestCase
{
    /**
     * @test
     * @dataProvider intClosureProvider
     */
    public function mapWithCorrectIntData(string $key, Closure $closure, string $outputKey, array $data) : void
    {
        $fieldMapper = new FieldMapper($key, $closure, $outputKey);

        $this->assertSame(123, $fieldMapper->map($data));
    }

    /**
     * @test
     * @dataProvider stringClosureProvider
     */
    public function mapWithCorrectStringData(string $key, Closure $closure, string $outputKey, array $data) : void
    {
        $fieldMapper = new FieldMapper($key, $closure, $outputKey);

        $this->assertSame('marek', $fieldMapper->map($data));
    }

    /**
     * @test
     * @dataProvider boolClosureProvider
     */
    public function mapWithCorrectBoolData(string $key, Closure $closure, string $outputKey, array $data) : void
    {
        $fieldMapper = new FieldMapper($key, $closure, $outputKey);

        $this->assertSame(true, $fieldMapper->map($data));
    }

//    /**
//     * @test
//     * @dataProvider undefinedKeyDataProvider
//     */
//    public function mapWithIncorrectKey(string $key, Closure $closure, string $outputKey, array $data) : void
//    {
//        $fieldMapper = new FieldMapper($key, $closure, $outputKey);
//
//        $this->expectException(UnsetKeyException::class);
//        $fieldMapper->map($data);
//    }

    /*************************** DATA PROVIDERS **********************************************/

    public function intClosureProvider() : array
    {
        /** @var Closure */
        $closure = function ($data) : int {
            return (int) $data;
        };

        return [
            'correct data 1' => [
                'id',
                $closure,
                '',
                $data = ['id' => '123'],
            ],
            'correct data 2' => [
                'id',
                $closure,
                'ticketId',
                $data = ['id' => '123'],
            ],
        ];
    }

    public function stringClosureProvider() : array
    {
        /** @var Closure */
        $closure = function ($data) : string {
            return (string) $data;
        };

        return [
            'correct data 3' => [
                'name',
                $closure,
                '',
                $data = ['name' => 'marek'],
            ],
            'correct data 4' => [
                'name',
                $closure,
                'Username',
                $data = ['name' => 'marek'],
            ],
        ];
    }

    public function boolClosureProvider() : array
    {
        /** @var Closure */
        $closure = function ($data) : bool {
            return (bool) $data;
        };

        return [
            'correct data 5' => [
                'active',
                $closure,
                '',
                $data = ['active' => true],
            ],
            'correct data 6' => [
                'active',
                $closure,
                'isActive',
                $data = ['active' => true],
            ],
        ];
    }

    public function undefinedKeyDataProvider() : array
    {
        /** @var Closure */
        $closure = function ($data) : string {
            return (string) $data;
        };

        return [
            'not existing key given 1' => [
                'aaaaa',
                $closure,
                'Username',
                $data = ['name' => 'marek'],
            ],
            'not existing key given 2' => [
                'idi',
                $closure,
                '',
                $data = ['id' => '5'],
            ],
        ];
    }
}
