<?php

namespace henrik\env;

use henrik\container\Container;
use henrik\container\ContainerModes;
use henrik\env\exceptions\ConfigurationFileNotFoundException;
use henrik\env\exceptions\ConfigurationNotExists;
use henrik\env\interfaces\ConfigParserInterface;

/**
 * Class Environment
 * @package henrik\env
 */
class Environment extends Container
{

    /**
     * @var ConfigParserInterface
     */
    private $configParser;

    /**
     * Environment constructor.
     * @param ConfigParserInterface $configParser
     */
    public function __construct(ConfigParserInterface $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     *
     * @param $file
     * @throws \henrik\container\UndefinedModeException
     *
     */
    public function load($file)
    {
        try {
            $this->checkFileIsExist($file);
            $data = $this->configParser->parse($file);
            $this->change_mode(ContainerModes::SINGLE_VALUE_MODE);
            $this->data = $data;
        } catch (ConfigurationFileNotFoundException $e) {
            exit($e->getMessage());
        }
    }

    /**
     *  The syntax of usage:
     *      $[object of environment class]->[ini file context][attr name]
     *      $env->db['db_user']
     *
     * @param $name
     * @return mixed
     * @throws ConfigurationNotExists
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            throw new ConfigurationNotExists();
        }
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
     * @param $file
     * @throws ConfigurationFileNotFoundException
     */
    public function checkFileIsExist($file)
    {
        if (!file_exists($file)) throw new ConfigurationFileNotFoundException("Your configuration file doesn't exists");
    }

    //TODO delete  naxer
    public function printData()
    {
        var_dump($this->data);
    }
}