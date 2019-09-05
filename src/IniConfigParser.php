<?php


namespace henrik\env;


use henrik\env\exceptions\ContextOrIdNotExistsException;
use henrik\env\exceptions\InvalidConfigSyntaxException;
use henrik\env\interfaces\ConfigParserInterface;

/**
 * Class IniConfigParser
 * @package henrik\env\parsers
 */
class IniConfigParser implements ConfigParserInterface
{
    /**
     * @var string
     */
    private $default_context = 'default';
    /**
     * @var string
     */
    private $context;
    /**
     * @var bool
     * If set to TRUE, it returns is a multidimensional array with
     * section names and settings included. Default is FALSE
     * @default value true
     */
    private $process_sections = true;
    /**
     * @var int
     * Available modes
     * INI_SCANNER_NORMAL (default)
     * INI_SCANNER_RAW (means option values will not be parsed)
     * INI_SCANNER_TYPED (means that boolean, null and integer types are preserved when possible. "true",
     *      "on", "yes" are converted to TRUE. "false", "off", "no", "none" are converted to FALSE.
     *      "null" is converted to NULL. Numeric strings are converted to integer type if possible)
     * @default value INI_SCANNER_TYPED
     */
    private $scanner_mode = INI_SCANNER_TYPED;

    /**
     * @param $file
     * @return array|mixed
     * @throws InvalidConfigSyntaxException
     */
    public function parse($file)
    {
        $handle = fopen($file, "r");
        $data = [];
        $this->context = $this->default_context;
        while (($line = fgets($handle)) !== false) {

            $line = $this->deleteCommentFromLine($line);
            if ($this->checkIsLineEmpty($line)) {
                continue;
            }
            $context_data = $this->checkIsContext($line);
            if (is_array($context_data) && !empty($context_data[0])) {
                $this->context = $context_data['key'];
                continue;
            }

            $line_data = explode('=', $line);
            if (count($line_data) > 1) {
                $value = $this->checkValueAndReturn($data, $line_data[1]);
            } else {
                throw new InvalidConfigSyntaxException();
            }
            $key = $this->normalizeValue($line_data[0]);
            $value = $this->normalizeValue($value);

            $data[$this->context][$key] = $value;
        }
        fclose($handle);
        return $data;
    }

    /**
     * @param $line
     * @return string
     */
    private function deleteCommentFromLine($line)
    {
        $comment_symbol_pose = strpos($line, ';');
        if ($comment_symbol_pose || $comment_symbol_pose === 0) {
            $uncommented_string = substr($line, 0, $comment_symbol_pose);
            return trim($uncommented_string);
        }
        return trim($line);
    }

    /**
     * @param $line
     * @return bool
     */
    private function checkIsLineEmpty($line)
    {
        $line = trim($line);
        return empty($line);
    }

    /**
     * @param $line
     * @return mixed
     */
    private function checkIsValueIsFromRelatedKey($line)
    {
        $pattern = '#\${(?<key>[^}]+)}#ixs';
        preg_match_all($pattern, $line, $matches);
        if (!empty($matches[0])) {
            return $matches;
        }
        return $line;
    }

    /**
     * @return bool
     */
    public function isProcessSections()
    {
        return $this->process_sections;
    }

    /**
     * @param bool $process_sections
     */
    public function setProcessSections($process_sections)
    {
        $this->process_sections = $process_sections;
    }

    /**
     * @return int
     */
    public function getScannerMode()
    {
        return $this->scanner_mode;
    }

    /**
     * @param int $scanner_mode
     */
    public function setScannerMode($scanner_mode)
    {
        $this->scanner_mode = $scanner_mode;
    }

    /**
     * @param $line
     * @return mixed
     */
    private function checkIsContext($line)
    {
        $pattern = '#\[(?<key>[^}]+)]#ixs';
        preg_match($pattern, $line, $matches);
        if ($matches) {
            return $matches;
        }
        return $line;
    }

    /**
     * @param $value
     * @return mixed|string
     */
    private function normalizeValue($value)
    {
        $new_value = str_replace('"', '', $value);
        $new_value = trim($new_value);
        return $new_value;
    }

    /**
     * @param $data
     * @param $value_part
     * @return mixed|string
     */
    private function checkValueAndReturn($data, $value_part)
    {
        $value = "";
        $res = $this->checkIsValueIsFromRelatedKey($value_part);
        if (is_array($res)) {
            $res_assoc_array = array_combine($res[0], $res['key']);
            foreach ($res_assoc_array as $key => $value) {
                $context_with_id_array = explode(':', $value);
                if (count($context_with_id_array) > 1) {
                    $context = $context_with_id_array[0];
                    $id = $context_with_id_array[1];
                    if (isset($data[$context]) && isset($data[$context][$id]))
                        $rel_id_value = $data[$context][$id];
                    else
                        throw new ContextOrIdNotExistsException();
                    $value = str_replace($key, $rel_id_value, $value_part);
                }
            }
        } else {
            $value = $res;
        }

        return $value;
    }
}