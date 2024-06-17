<?php

declare(strict_types=1);

namespace Henrik\Env\Exceptions;

use Throwable;

/**
 * Class ConfigurationFileNotFoundException.
 */
class ConfigurationFileNotFoundException extends EnvironmentException
{
    public function __construct(string $file, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf("Your configuration file `%s` doesn't exists", $file), $code, $previous);
    }
}