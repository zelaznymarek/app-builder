<?php

declare(strict_types = 1);

namespace AppBuilder\Application\Utils\Mapper;

use AppBuilder\Application\Module\Jira\Exception\MapperRepetitionException;

class ArrayMapper implements Mapper
{
    /** @var string */
    private $key;

    /** @var string */
    private $outputKey;

    /** @var array */
    private $mappers = [];

    public function __construct(string $key, array $fieldMappers, string $outputKey = '')
    {
        $this->key       = $key;
        $this->outputKey = $outputKey;

        if ('' === $outputKey) {
            $this->outputKey = $key;
        }
        foreach ($fieldMappers as $mapper) {
            if (array_key_exists($mapper->key(), $this->mappers)) {
                throw new MapperRepetitionException('Mapper with key: ' . $mapper->key() . ' already exists');
            }
            $this->mappers[$mapper->key()] = $mapper;
        }
    }

    public function key() : string
    {
        return $this->key;
    }

    /** Returns outputKey passed in constructor. If not passed, returns key value */
    public function outputKey() : string
    {
        return $this->outputKey;
    }

    /**
     * Returns associative array with passed mappers.
     */
    public function map(array $passedData, array $context = [])
    {
        $data   = $passedData[$this->key];
        $result = [];
        foreach ($this->mappers as $mapper) {
            $this->outputKey = $mapper->outputKey();
            if (null === $data || !array_key_exists($mapper->key(), $data)) {
                $result = null;
            } else {
                $result = $mapper->map($data, $context ?: $data);
            }
        }

        return $result;
    }
}
