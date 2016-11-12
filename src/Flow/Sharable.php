<?php

namespace Simples\Core\Flow;

/**
 * Class Share
 * @package Simples\Core\Flow
 */
trait Sharable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param $key
     * @param string $data
     * @return $this|mixed
     */
    public function data($key, $data = '')
    {
        if ($data === '') {
            return $this->out($key);
        }
        $this->in($key, $data);

        return $this;
    }

    /**
     * @param $index
     * @param $value
     * @return $this
     */
    public function in($index, $value)
    {
        $this->data[$index] = $value;
        if (is_null($value)) {
            unset($this->data[$index]);
        }

        return $this;
    }

    /**
     * @param $index
     * @param null $default
     * @return mixed
     */
    public function out($index, $default = null)
    {
        return isset($this->data[$index]) ? $this->data[$index] : $default;
    }
}