<?php

namespace BFACP\Helpers;

/**
 * Class PermissionName.
 */
class PermissionName extends Main
{
    /**
     * @var array
     */
    public $chunks;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->chunks = explode(".", $name);
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        return $this->chunks[0];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getChunk($key)
    {
        return $this->chunks[$key];
    }

    /**
     * @return mixed
     */
    public function getLast()
    {
        return $this->chunks[count($this->chunks) - 1];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(".", $this->chunks);
    }
}
