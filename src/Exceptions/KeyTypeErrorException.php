<?php

namespace Henrik\Env\Exceptions;

use Throwable;

class KeyTypeErrorException extends EnvironmentException
{
    public function __construct(string $keyType, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('The key type must be  `string` but `%s` given', $keyType), $code, $previous);
    }
}