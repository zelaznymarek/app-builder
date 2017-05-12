<?php

declare(strict_types=1);

namespace Pvg\Application\Module\Jira\Factory;

use Closure;
use Pvg\Application\Utils\Mapper\FieldMapper;

class FieldMapperFactory
{
    public static function create(string $key, Closure $closure, string $outputKey = '') : FieldMapper
    {
        return new FieldMapper($key, $closure, $outputKey);
    }
}
