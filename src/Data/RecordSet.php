<?php

namespace Simples\Core\Data;

use Iterator;
use Simples\Core\Unit\Origin;

/**
 * Class RecordSet
 * @package Simples\Core\Domain
 */
class RecordSet extends Origin implements Iterator
{
    /**
     * @var array
     */
    private $records = [];

    /**
     * @var
     */
    private $items;

    /**
     * RecordSet constructor.
     * @param $array
     * @param array $items
     */
    public function __construct($array, $items = [])
    {
        if (is_array($array)) {
            $this->records = $array;
        }
        $this->items = $items;
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
        return new Record($var, true, $this->items);
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
        $var = ($key !== NULL && $key !== FALSE);
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