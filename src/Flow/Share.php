<?php

namespace Simples\Core\Flow;


class Share
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param $index
     * @param $value
     * @return $this
     */
    public function in($index, $value)
    {
        $this->data[$index] = $value;

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