<?php

namespace Henrik\Env;

use Henrik\Env\Exceptions\ContextOrIdNotExistsException;
use Henrik\Env\Exceptions\FileNotExistsException;
use Henrik\Env\Exceptions\InvalidConfigSyntaxException;

/**
 * Class IniConfigParser.
 */
class IniConfigParser implements ConfigParserInterface
{
    /**
     * @var string
     */
    private string $defaultContext = 'default';
    /**
     * @var string
     */
    private string $context;
    /**
     * @var bool
     *           If set to TRUE, it returns is a multidimensional array with
     *           section names and settings included. Default is FALSE
     *
     * @default value true
     */
    private bool $processSections = true;
    /**
     * @var int
     *          Available modes
     *          INI_SCANNER_NORMAL (default)
     *          INI_SCANNER_RAW (means option values will not be parsed)
     *          INI_SCANNER_TYPED (means that boolean, null and integer types are preserved when possible. "true",
     *          "on", "yes" are converted to TRUE. "false", "off", "no", "none" are converted to FALSE.
     *          "null" is converted to NULL. Numeric strings are converted to integer type if possible)
     *
     * @default value INI_SCANNER_TYPED
     */
    private int $scannerMode = INI_SCANNER_TYPED;

    /**
     * @param $file
     *
     * @throws FileNotExistsException
     * @throws InvalidConfigSyntaxException|ContextOrIdNotExistsException
     *
     * @return array<string, mixed>
     */
    public function parse($file): array
    {
        $handle        = fopen($file, 'r');
        $data          = [];
        $this->context = $this->defaultContext;

        if (is_resource($handle)) {
            while (($line = fgets($handle)) !== false) {

                $line = $this->deleteCommentFromLine($line);

                if ($this->checkIsLineEmpty($line)) {
                    continue;
                }
                $contextData = $this->checkIsContext($line);

                if (is_array($contextData) && !empty($contextData[0])) {
                    $this->context = $contextData['key'];

                    continue;
                }

                $lineData = explode('=', $line);

                if (count($lineData) <= 1) {
                    throw new InvalidConfigSyntaxException();
                }

                $value = $this->checkValueAndReturn($data, $lineData[1]);

                $key   = $this->normalizeValue($lineData[0]);
                $value = $this->normalizeValue($value);

                $data[$this->context][$key] = $value;

            }
            fclose($handle);

            return $data;
        }

        throw new FileNotExistsException();
    }

    /**
     * @return bool
     */
    public function isProcessSections(): bool
    {
        return $this->processSections;
    }

    /**
     * @param bool $processSections
     */
    public function setProcessSections(bool $processSections): void
    {
        $this->processSections = $processSections;
    }

    /**
     * @return int
     */
    public function getScannerMode(): int
    {
        return $this->scannerMode;
    }

    /**
     * @param int $scannerMode
     */
    public function setScannerMode(int $scannerMode): void
    {
        $this->scannerMode = $scannerMode;
    }

    /**
     * @param string $line
     *
     * @return string
     */
    private function deleteCommentFromLine(string $line): string
    {
        $commentSymbolPose = strpos($line, ';');

        if ($commentSymbolPose || $commentSymbolPose === 0) {
            $uncommentedString = substr($line, 0, $commentSymbolPose);

            return trim($uncommentedString);
        }

        return trim($line);
    }

    /**
     * @param string $line
     *
     * @return bool
     */
    private function checkIsLineEmpty(string $line): bool
    {
        $line = trim($line);

        return empty($line);
    }

    /**
     * @param string $line
     *
     * @return string[]|string
     */
    private function checkIsValueIsFromRelatedKey(string $line): array|string
    {
        $pattern = '#\${(?<key>[^}]+)}#ixs';
        preg_match_all($pattern, $line, $matches);

        if (!empty($matches[0])) {
            return $matches;
        }

        return $line;
    }

    /**
     * @param string $line
     *
     * @return string|string[]
     */
    private function checkIsContext(string $line): array|string
    {
        $pattern = '#\[(?<key>[^}]+)]#ixs';
        preg_match($pattern, $line, $matches);

        if ($matches) {
            return $matches;
        }

        return $line;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function normalizeValue(string $value): string
    {
        return trim(str_replace('"', '', $value));
    }

    /**
     * @param array  $data
     * @param string $valuePart
     *
     * @throws ContextOrIdNotExistsException
     *
     * @return mixed
     */
    private function checkValueAndReturn(array $data, string $valuePart): mixed
    {
        $res   = $this->checkIsValueIsFromRelatedKey($valuePart);
        $value = $res;

        if (is_array($res)) {
            $resultFromAssocArray = array_combine($res[0], $res['key']);

            foreach ($resultFromAssocArray as $key => $value) {
                $contextWithIdArray = explode(':', $value);

                if (count($contextWithIdArray) > 1) {
                    $context = $contextWithIdArray[0];
                    $id      = $contextWithIdArray[1];

                    if (!isset($data[$context], $data[$context][$id])) {
                        throw new ContextOrIdNotExistsException();
                    }
                    $relIdValue = $data[$context][$id];
                    $value      = str_replace($key, $relIdValue, $valuePart);
                }
            }
        }

        return $value;
    }
}