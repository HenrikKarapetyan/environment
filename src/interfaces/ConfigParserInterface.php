<?php


namespace henrik\env\interfaces;


/**
 * Interface ConfigParserInterface
 * @package henrik\env\interfaces
 */
interface ConfigParserInterface
{
    /**
     * the parsed  data must be like this format
     *      db
     *          db_user
     *          db_pass
     * @param $file
     * @return mixed
     *
     *
     */
    public function parse($file);
}