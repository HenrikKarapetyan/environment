<?php

declare(strict_types=1);

namespace Henrik\Env;

use Henrik\Contracts\Environment\EnvironmentParserInterface;
use Henrik\Env\Exceptions\ContextOrIdNotExistsException;

/**
 * Class IniEnvironmentParser.
 */
class IniEnvironmentParser implements EnvironmentParserInterface
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $data = [];

    public function parse($file): array
    {
        $parsedData = parse_ini_file($file, true, INI_SCANNER_RAW);

        if (is_array($parsedData)) {
            $this->data = $parsedData;

            foreach ($this->data as $section => $params) {
                $this->data[$section] = $this->detectVariablesAndNormalizeValues($params);
            }

            return $this->data;
        }

        return [];
    }

    /**
     * @param string $valuePart
     *
     * @throws ContextOrIdNotExistsException
     *
     * @return string
     */
    public function checkValueAndReturn(string $valuePart): string
    {
        /** @var mixed $res */
        $res = $this->checkIsValueFromRelatedKey($valuePart);

        $value = $valuePart;

        if (is_array($res)) {
            $resultFromAssocArray = array_combine($res[0], $res['key']);

            foreach ($resultFromAssocArray as $key => $value) {
                $contextWithIdArray = explode(':', $value);

                if (count($contextWithIdArray) > 1) {
                    $context = $contextWithIdArray[0];
                    $id      = $contextWithIdArray[1];

                    if (!isset($this->data[$context], $this->data[$context][$id])) {
                        throw new ContextOrIdNotExistsException();
                    }

                    /** @var string $relIdValue */
                    $relIdValue = $this->data[$context][$id];
                    /** @var string $value */
                    $value = str_replace($key, $relIdValue, $valuePart);
                }
            }
        }

        return $value;
    }

    /**
     * @param string $line
     *
     * @return array<int|string>|array<int|string, array<int|string>>|string
     */
    private function checkIsValueFromRelatedKey(string $line): array|string
    {
        $pattern = '#\${(?<key>[^}]+)}#ixs';
        preg_match_all($pattern, $line, $matches);

        if (!empty($matches[0])) {
            return $matches;
        }

        return $line;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws ContextOrIdNotExistsException
     *
     * @return array<string, mixed>
     */
    private function detectVariablesAndNormalizeValues(array $data): array
    {
        foreach ($data as &$params) {

            if (is_array($params)) {
                $params = $this->detectVariablesAndNormalizeValues($params);

                continue;
            }

            if (is_string($params)) {
                $params = $this->checkValueAndReturn($params);
            }
        }

        return $data;
    }
}