<?php

declare(strict_types=1);

namespace Henrik\Env;

use Henrik\Container\Container;
use Henrik\Container\ContainerModes;
use Henrik\Container\Exceptions\KeyAlreadyExistsException;
use Henrik\Container\Exceptions\KeyNotFoundException;
use Henrik\Container\Exceptions\UndefinedModeException;
use Henrik\Contracts\Environment\EnvironmentInterface;
use Henrik\Contracts\Environment\EnvironmentParserInterface;
use Henrik\Env\Exceptions\ConfigurationFileNotFoundException;
use Henrik\Env\Exceptions\KeyTypeErrorException;
use Henrik\Env\Exceptions\UndefinedIdException;

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

    /**
     * @param string     $id
     * @param mixed|null $default
     *
     * @throws KeyNotFoundException
     * @throws UndefinedIdException
     */
    public function get(string $id, mixed $default = null): mixed
    {
        if (str_contains($id, '.')) {
            $idParts            = explode('.', $id);
            $segmentName        = array_shift($idParts);
            $valueFromContainer = parent::get($segmentName);

            if (is_array($valueFromContainer)) {

                return $this->getValueFromArray($valueFromContainer, $idParts);
            }

        }

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
                $this->data = array_merge($this->data, $data);
            }

            $this->changeMode(ContainerModes::MULTIPLE_VALUE_MODE);
        } catch (ConfigurationFileNotFoundException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param mixed $offset
     *
     * @throws KeyTypeErrorException
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        if (is_string($offset)) {
            return $this->has($offset);
        }

        throw new KeyTypeErrorException(gettype($offset));
    }

    /**
     * {@inheritDoc}
     *
     * @throws KeyNotFoundException
     * @throws KeyTypeErrorException
     * @throws UndefinedIdException
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (is_string($offset)) {
            return $this->get($offset);
        }

        throw new KeyTypeErrorException(gettype($offset));
    }

    /**
     * {@inheritDoc}
     *
     * @throws KeyTypeErrorException
     * @throws KeyAlreadyExistsException
     */
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

    /**
     * @param array<string, mixed> $valueFromContainer
     * @param array<string>        $idParts
     *
     * @throws UndefinedIdException
     */
    private function getValueFromArray(array $valueFromContainer, array $idParts): mixed
    {
        $id = array_shift($idParts);

        if (isset($valueFromContainer[$id])) {

            if (is_array($valueFromContainer[$id]) && !empty($idParts)) {
                return $this->getValueFromArray($valueFromContainer[$id], $idParts);
            }

            return $valueFromContainer[$id];
        }

        return null;
    }
}