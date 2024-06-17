<?php

declare(strict_types=1);

namespace Henrik\Env\Test;

use Henrik\Container\Exceptions\KeyNotFoundException;
use Henrik\Env\Environment;
use Henrik\Env\Exceptions\ConfigurationFileNotFoundException;
use Henrik\Env\Exceptions\KeyTypeErrorException;
use Henrik\Env\IniEnvironmentParser;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    /**
     * @throws KeyNotFoundException
     * @throws ConfigurationFileNotFoundException
     */
    public function testEnvironmentValues(): void
    {
        $iniConfigParser = new IniEnvironmentParser();
        $env             = new Environment($iniConfigParser);

        $env->load(__DIR__ . '/stubs/env.ini');
        $this->assertEquals('dev', $env->get('app.env'));
        $this->assertEquals(['dev', 'prod', 'local'], $env->get('app.environments'));

        $this->assertEquals('/../src', $env->get('parameters.sourcesRootPath'));
        $this->assertEquals('/../public', $env->get('parameters.assetsDir'));
        $this->assertEquals('\\Hk\\App', $env->get('parameters.rootNamespace'));
        $this->assertEquals('/../var/session/', $env->get('parameters.sessionSavePath'));
        $this->assertEquals('/../var/logs/', $env->get('parameters.logsSaveDirectory'));

        $this->assertEquals(['Core', 'Entity', 'Repository'], $env->get('parameters.serviceExcludedPaths'));

        $env->load(__DIR__ . '/stubs/prod.env.ini');

        $this->assertEquals('prod', $env->get('app.env'));
    }

    public function testBadFilePath(): void
    {
        $this->expectException(ConfigurationFileNotFoundException::class);

        $iniConfigParser = new IniEnvironmentParser();
        $env             = new Environment($iniConfigParser);

        $env->load(__DIR__ . '/stubs/env2.ini');
    }

    public function testArrayAccessForEnvironment(): void
    {
        $iniConfigParser = new IniEnvironmentParser();
        $env             = new Environment($iniConfigParser);

        $env->load(__DIR__ . '/stubs/env.ini');

        $this->assertTrue(isset($env['app']));
        $this->assertIsArray($env['app']);

        unset($env['app']);

        $this->assertFalse(isset($env['app']));
        $this->assertEmpty($env['app']);

        $dataArray  = ['name' => 'developer', 'lastname' => 'developer'];
        $env['app'] = $dataArray;
        $this->assertEquals($dataArray, $env['app']);

        $this->expectException(KeyTypeErrorException::class);
        $val = $env[1];
        $this->assertNull($val);
    }

    /**
     * @throws KeyNotFoundException
     * @throws ConfigurationFileNotFoundException
     */
    public function testGetEnvironmentValueByMultiPartId(): void
    {
        $iniConfigParser = new IniEnvironmentParser();
        $env             = new Environment($iniConfigParser);

        $env->load(__DIR__ . '/stubs/env.ini');
        $this->assertEquals('dev', $env->get('app.env'));
        $this->assertEquals(['dev', 'prod', 'local'], $env->get('app.environments'));
        $this->assertEquals('dev', $env->get('app.environments.0'));
        $this->assertEquals('prod', $env->get('app.environments.1'));
        $this->assertEquals('local', $env->get('app.environments.2'));
        $this->assertEquals(null, $env->get('app.environments.3'));
    }

}