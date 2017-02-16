<?php

namespace Simples\Core\Data;

use Iterator;
use Simples\Core\Unit\Origin;

/**
 * Class AbstractCollection
 * @package Simples\Core\Data
 */
class AbstractCollection extends Origin implements Iterator
{
    /**
     * @var array
     */
    protected $records = [];

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
            return Record::make($var);
        }
        return Record::make([]);
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
}
