<?php

namespace Simples\Core\Data;

use Iterator;
use Simples\Core\Error\RunTimeError;
use Simples\Core\Unit\Origin;

/**
 * Class Collection
 * @property Collection map
 * @property Collection filter
 * @property Collection each
 * @package Simples\Core\Domain
 */
class Collection extends Origin implements Iterator
{
    /**
     * @var array
     */
    private $records = [];

    /**
     * @var array
     */
    private $higher = [];

    /**
     * Collection constructor.
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->records = $array;
    }

    /**
     * Factory constructor
     * @param array $array
     * @return Collection
     */
    public static function create(array $array = []): Collection
    {
        return new static($array);
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return Collection
     * @throws RunTimeError
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    function __get($name): Collection
    {
        if (!method_exists($this, $name)) {
            throw new RunTimeError("Method '{$name}' not found");
        }
        $this->higher[] = $name;
        return $this;
    }

    /**
     * Ex.:
     *   $result = Collection::create([new Example('apple'), new Example('orange')])
     *      ->map->each->getFruit()->getRecords();
     *   var_dump($result);
     *   ["elppa", "egnaro"]
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $records = $this->records;
        foreach ($this->higher as $higher) {
            $records = $this->{$higher}(function ($value) use ($name, $arguments) {
                return call_user_func_array([$value, $name], $arguments);
            });
        }
        $this->higher = [];
        return $records;
    }

    /**
     * @param callable $callback
     * @return Collection
     */
    public function each(callable $callback): Collection
    {
        foreach ($this->records as $key => $record) {
            $this->records[$key] = $callback($record);
        }
        return $this;
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback)
    {
        return array_map($callback, $this->records);
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback)
    {
        return array_filter($this->records, $callback);
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
            return Record::create($var);
        }
        return Record::create([]);
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
