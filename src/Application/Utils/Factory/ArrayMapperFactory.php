<?php

declare(strict_types=1);

namespace Pvg\Application\Utils\Factory;

use Pvg\Application\Utils\Mapper\ArrayMapper;

class ArrayMapperFactory
{
    public static function create(string $key, array $mappers) : ArrayMapper
    {
        return new ArrayMapper($key, $mappers);
    }
}
