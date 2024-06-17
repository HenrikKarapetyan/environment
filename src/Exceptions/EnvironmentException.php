<?php

declare(strict_types=1);

namespace Henrik\Env\Exceptions;

use Exception;
use Throwable;

/**
 * Class EnvironmentException.
 */
class EnvironmentException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}