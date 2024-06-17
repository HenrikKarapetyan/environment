<?php

namespace Henrik\Env\Traits;

use Henrik\Container\Exceptions\KeyAlreadyExistsException;
use Henrik\Container\Exceptions\KeyNotFoundException;
use Henrik\Env\Exceptions\KeyTypeErrorException;

trait EnvironmentArrayAccessTrait
{
    /**
     * @param mixed $offset
     *
     * @throws KeyTypeErrorException
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        $this->checkIsKeyValid($offset);

        /**
         * @var string $offset
         */
        return $this->has($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @throws KeyNotFoundException
     * @throws KeyTypeErrorException
     */
    public function offsetGet(mixed $offset): mixed
    {
        $this->checkIsKeyValid($offset);

        /**
         * @var string $offset
         */
        return $this->get($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @throws KeyTypeErrorException
     * @throws KeyAlreadyExistsException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->checkIsKeyValid($offset);
        /**
         * @var string $offset
         */
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     *
     * @throws KeyTypeErrorException
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->checkIsKeyValid($offset);
        /**
         * @var string $offset
         */
        $this->delete($offset);
    }

    /**
     * @param mixed $key
     *
     * @throws KeyTypeErrorException
     */
    public function checkIsKeyValid(mixed $key): void
    {
        if (!is_string($key)) {
            throw new KeyTypeErrorException(gettype($key));
        }
    }
}