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

    private bool $processSections = true;

    private int $scannerMode = INI_SCANNER_RAW;

    /**
     * @param string $file
     *
     * @throws ContextOrIdNotExistsException
     *
     * @return array<string, array<string, mixed>>
     */
    public function parse(string $file): array
    {
        $parsedData = parse_ini_file($file, $this->isProcessSections(), $this->getScannerMode());

        if ($parsedData) {
            $this->setData($parsedData);

            foreach ($this->getData() as $section => $params) {
                $this->getData()[$section] = $this->detectVariablesAndNormalizeValues($params);
            }

            return $this->getData();
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
     * @return array<string, array<string, mixed>>
     */
    public function &getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, array<string, mixed>> $data
     *
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function isProcessSections(): bool
    {
        return $this->processSections;
    }

    public function setProcessSections(bool $processSections): void
    {
        $this->processSections = $processSections;
    }

    public function getScannerMode(): int
    {
        return $this->scannerMode;
    }

    public function setScannerMode(int $scannerMode): void
    {
        $this->scannerMode = $scannerMode;
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