<?php

declare(strict_types=1);

namespace Henrik\Env;

use Henrik\Container\Container;
use Henrik\Container\Exceptions\KeyNotFoundException;
use Henrik\Contracts\Environment\EnvironmentInterface;
use Henrik\Contracts\Environment\EnvironmentParserInterface;
use Henrik\Env\Exceptions\ConfigurationFileNotFoundException;
use Henrik\Env\Traits\EnvironmentArrayAccessTrait;

/**
 * Class Environment.
 */
class Environment extends Container implements EnvironmentInterface
{
    use EnvironmentArrayAccessTrait;

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
     * @param string     $id
     * @param mixed|null $default
     *
     * @throws KeyNotFoundException
     *
     * @return mixed
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
     * @throws ConfigurationFileNotFoundException
     */
    public function load(string $file): void
    {
        $this->checkFileIsExist($file);
        $data = $this->configParser->parse($file);

        if ($data) {
            $this->data = array_merge($this->data, $data);
        }
    }

    /**
     * @param string $file
     *
     * @throws ConfigurationFileNotFoundException
     */
    private function checkFileIsExist(string $file): void
    {
        if (!file_exists($file)) {
            throw new ConfigurationFileNotFoundException($file);
        }
    }

    /**
     * @param array<string, mixed> $valueFromContainer
     * @param array<string>        $idParts
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