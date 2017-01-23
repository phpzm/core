<?php

namespace Simples\Core\Data;

use Iterator;
use Simples\Core\Unit\Origin;

/**
 * Class Collection
 * @package Simples\Core\Domain
 */
class Collection extends Origin implements Iterator
{
    /**
     * @var array
     */
    private $records = [];

    /**
     * @var mixed
     */
    private $instance;

    /**
     * Collection constructor.
     * @param $array
     * @param $instance
     */
    public function __construct($array, $instance = null)
    {
        if (is_array($array)) {
            $this->records = $array;
        }
        $this->instance = $instance;
    }

    /**
     * @param $instance
     * @return $this
     */
    public function with($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Collection
     */
    public function __call($name, $arguments)
    {
        $instance = $this->instance;
        if ($instance) {
            return $this->map(function($value, $key) use ($instance, $name, $arguments) {
                return call_user_func_array([$instance, $name], array_merge($key, $value, $arguments));
            });
        }
        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this->records as $record) {
            if ($callback($record) === false) {
                break;
            }
        }
        return $this;
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->records);
    }

    /**
     *
     */
    public function rewind()
    {
        reset($this->records);
    }

    /**
     * @return Record
     */
    public function current()
    {
        $var = current($this->records);
        if ($var) {
            return new Record($var);
        }
        return new Record([]);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        $var = key($this->records);
        return $var;
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $var = next($this->records);
        return $var;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = key($this->records);
        $var = ($key !== null && $key !== false);
        return $var;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function map($closure)
    {
        $this->records = array_map($closure, $this->records);

        return $this;
    }
}
