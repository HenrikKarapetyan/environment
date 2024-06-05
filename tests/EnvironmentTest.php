<?php

namespace Henrik\Env\Test;
use Henrik\Container\Exceptions\KeyNotFoundException;
use Henrik\Container\Exceptions\UndefinedModeException;
use Henrik\Env\Environment;
use Henrik\Env\IniEnvironmentParser;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{

    /**
     * @throws KeyNotFoundException
     * @throws UndefinedModeException
     */
    public function testEnvironment():void
    {
        $iniConfigParser = new IniEnvironmentParser();
        $env             = new Environment($iniConfigParser);

        $env->load(__DIR__.'/stubs/simple.ini');

        $this->assertEquals('dev', $env->get('app')['env']);
        $this->assertEquals(['dev','prod','local'], $env->get('app')['environments']);


        $this->assertEquals('/../src', $env->get('parameters')['sourcesRootPath']);
        $this->assertEquals('/../public', $env->get('parameters')['assetsDir']);
        $this->assertEquals('\\Hk\\App', $env->get('parameters')['rootNamespace']);
        $this->assertEquals('/../var/session/', $env->get('parameters')['sessionSavePath']);
        $this->assertEquals('/../var/logs/', $env->get('parameters')['logsSaveDirectory']);

        $this->assertEquals(['Core','Entity','Repository'], $env->get('parameters')['serviceExcludedPaths']);


        $env->load(__DIR__.'/stubs/prod.env.ini');
        $this->assertEquals('prod', $env->get('app')['env']);
    }
}