<?php

declare(strict_types=1);

namespace Pvg\Application\Utils\Mapper;

use Pvg\Application\Module\Jira\Exception\MapperRepetitionException;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;

class ArrayMapper
{
    /** @var string */
    private $key;

    /** @var array */
    private $mappers = [];

    public function __construct(string $key, array $fieldMappers)
    {
        $this->key = $key;
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

    /**
     * Returns associative array with passed mappers.
     *
     * @throws UnsetKeyException
     */
    public function map(array $data, array $context = []) : array
    {
        $result = [];
        foreach ($this->mappers as $mapper) {
            if (!array_key_exists($mapper->key(), $data)) {
                throw new UnsetKeyException('Key = ' . $mapper->key() . ' not found.');
            }
            $result[$mapper->outputKey()] = $mapper->map($data, $context ?: $data);
        }

        return $result;
    }
}
