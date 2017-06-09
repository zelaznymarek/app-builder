<?php

declare(strict_types = 1);

namespace Pvg\Application\Utils\Mapper;

interface Mapper
{
    /** Returns key passed in constructor */
    public function key() : string;

    /** Returns outputKey passed in constructor. If not passed, returns key value */
    public function outputKey() : string;

    /** Returns data mapped to associative array */
    public function map(array $data, array $context);
}
