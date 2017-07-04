<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Utils\Mapper;

use Closure;

class FieldMapper implements Mapper
{
    /** @var string */
    private $key;

    /** @var string */
    private $outputKey;

    /** @var Closure */
    private $closure;

    public function __construct(string $key, Closure $closure, string $outputKey = '')
    {
        if ($outputKey === '') {
            $this->outputKey = $key;
        } else {
            $this->outputKey = $outputKey;
        }
        $this->key     = $key;
        $this->closure = $closure;
    }

    public function key() : string
    {
        return $this->key;
    }

    public function outputKey() : string
    {
        return $this->outputKey;
    }

    /**
     * Returns associative array with mapped data and context.
     */
    public function map(array $data, array $context = [])
    {
        if (!array_key_exists($this->key, $data)) {
            return null;
        }

        return ($this->closure)($data[$this->key], $context ?? $data);
    }
}
