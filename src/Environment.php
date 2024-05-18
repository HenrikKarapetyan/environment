<?php

namespace Henrik\Env;

use henrik\container\Container;
use henrik\container\ContainerModes;
use henrik\container\exceptions\UndefinedModeException;
use Henrik\Env\Exceptions\ConfigurationFileNotFoundException;
use Henrik\Env\Exceptions\ConfigurationNotExists;

/**
 * Class Environment.
 */
class Environment extends Container
{
    /**
     * @var ConfigParserInterface
     */
    private ConfigParserInterface $configParser;

    /**
     * Environment constructor.
     *
     * @param ConfigParserInterface $configParser
     */
    public function __construct(ConfigParserInterface $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     *  The syntax of usage:
     *      $[object of environment class]->[ini file context][attr name]
     *      $env->db['db_user']
     *
     * @param $name
     *
     * @throws ConfigurationNotExists
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        throw new ConfigurationNotExists();

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
     * @param string $file
     *
     * @throws UndefinedModeException
     */
    public function load(string $file): void
    {
        try {
            $this->checkFileIsExist($file);
            $data = $this->configParser->parse($file);
            $this->changeMode(ContainerModes::SINGLE_VALUE_MODE);
            $this->data = $data;
        } catch (ConfigurationFileNotFoundException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $file
     *
     * @throws ConfigurationFileNotFoundException
     */
    public function checkFileIsExist(string $file): void
    {
        if (!file_exists($file)) {
            throw new ConfigurationFileNotFoundException("Your configuration file doesn't exists");
        }
    }

    // TODO delete  naxer
    public function printData(): void
    {
        var_dump($this->data);
    }
}