<?php

namespace Henrik\Env\Test;

use Exception;
use Henrik\Env\Exceptions\ContextOrIdNotExistsException;
use Henrik\Env\IniEnvironmentParser;
use PHPUnit\Framework\TestCase;

class IniEnvironmentParserTest extends TestCase
{
    public function testUnknownVariableInsideConfigFile(): void
    {
        $this->expectException(ContextOrIdNotExistsException::class);

        $iniConfigParser = new IniEnvironmentParser();
        $iniConfigParser->parse(__DIR__ . '/stubs/unknown-variable.env.ini');
    }

    /**
     * @throws ContextOrIdNotExistsException
     */
    public function testUnknownFile(): void
    {
        $iniConfigParser = new IniEnvironmentParser();
        $iniConfigParser->setScannerMode(INI_SCANNER_RAW);
        $iniConfigParser->setProcessSections(true);
        $data = $iniConfigParser->parse(__DIR__ . '/stubs/env2.ini');
        $this->assertEmpty($data);
    }
}
