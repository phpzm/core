<?php

namespace Simples\Core\Model\Resources;

/**
 * Class Cache
 * @package Simples\Core\Model\Resources
 */
trait Cache
{
    /**
     * Array useful to store values in processing
     * @var array
     */
    private $cache = [];

    /**
     * @param string $key
     * @return mixed
     */
    public function cacheGet(string $key)
    {
        return off($this->cache, $key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function cacheHas(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    /**
     * @param string $key
     * @param $data
     * @return $this
     */
    public function cacheSet(string $key, $data)
    {
        $this->cache[$key] = $data;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function cacheRemove(string $key)
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
        }
        return $this;
    }
}
