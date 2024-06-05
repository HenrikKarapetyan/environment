<?php

declare(strict_types=1);

namespace Henrik\Env;

use Henrik\Container\Container;
use Henrik\Container\ContainerModes;
use Henrik\Container\Exceptions\UndefinedModeException;
use Henrik\Contracts\Environment\EnvironmentInterface;
use Henrik\Contracts\Environment\EnvironmentParserInterface;
use Henrik\Env\Exceptions\ConfigurationFileNotFoundException;
use Henrik\Env\Exceptions\KeyTypeErrorException;

/**
 * Class Environment.
 */
class Environment extends Container implements EnvironmentInterface
{
    /**
     * @var EnvironmentParserInterface
     */
    private EnvironmentParserInterface $configParser;

    /**
     * Environment constructor.
     *
     * @param EnvironmentParserInterface $configParser
     */
    public function __construct(EnvironmentParserInterface $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function get(string $id, mixed $default = null): mixed
    {
        if ($this->has($id)) {
            return parent::get($id);
        }

        return $default;
    }

    /**
     * @param string $file
     *
     * @throws UndefinedModeException
     */
    public function load(string $file): void
    {
        try {
            $this->checkFileIsExist($file);
            $data = $this->configParser->parse($file);

            if ($data) {
                $this->data = array_merge_recursive($data, $this->data);
            }

            $this->changeMode(ContainerModes::MULTIPLE_VALUE_MODE);
            $this->data = $data;
        } catch (ConfigurationFileNotFoundException $e) {
            echo $e->getMessage();
        }
    }

    // TODO delete  naxer
    public function printData(): void
    {
        var_dump($this->data);
    }

    public function offsetExists(mixed $offset): bool
    {
        if (is_string($offset)) {
            return $this->has($offset);
        }

        throw new KeyTypeErrorException(gettype($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (is_string($offset)) {
            return $this->get($offset);
        }

        throw new KeyTypeErrorException(gettype($offset));
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

        if (is_string($offset)) {
            $this->set($offset, $value);
        }

        throw new KeyTypeErrorException(gettype($offset));
    }

    /**
     * @param mixed $offset
     *
     * @throws KeyTypeErrorException
     */
    public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset)) {
            $this->delete($offset);
        }

        throw new KeyTypeErrorException(gettype($offset));
    }

    /**
     * @param string $file
     *
     * @throws ConfigurationFileNotFoundException
     */
    private function checkFileIsExist(string $file): void
    {
        if (!file_exists($file)) {
            throw new ConfigurationFileNotFoundException("Your configuration file doesn't exists");
        }
    }
}