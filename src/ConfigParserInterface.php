<?php

namespace Henrik\Env;

/**
 * Interface ConfigParserInterface.
 */
interface ConfigParserInterface
{
    /**
     * the parsed  data must be like this format
     *      db
     *          db_user
     *          db_pass.
     *
     * @param string $file
     *
     * @return array<string, mixed>
     */
    public function parse(string $file): array;
}