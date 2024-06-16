<?php

declare(strict_types=1);

namespace Henrik\Env\Exceptions;

use Throwable;

class UndefinedIdException extends EnvironmentException
{
    public function __construct(string $id, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('The id `%s` not found!', $id), $code, $previous);
    }
}