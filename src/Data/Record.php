<?php

namespace Simples\Core\Data;

use IteratorAggregate;
use JsonSerializable;
use Simples\Core\Error\RunTimeError;
use Simples\Core\Helper\JSON;
use Simples\Core\Unit\Origin;
use stdClass;

/**
 * Class Record
 * @property string json
 * @package Simples\Core\Domain
 */
class Record extends Origin implements IteratorAggregate, JsonSerializable
{
    /**
     * Values accessible of Record
     * @var array
     */
    private $public;

    /**
     * Values hidden into Record
     * @var array
     */
    private $private;

    /**
     * Define if the can be expanded
     * @var bool
     */
    private $injectable;

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * Record constructor.
     * @param array|stdClass $data
     * @param bool $injectable
     */
    public function __construct($data, bool $injectable = true)
    {
        $this->public = (array)$data;
        $this->injectable = $injectable;
        $this->private = [];
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * Factory constructor
     * @param array|stdClass $data
     * @param bool $injectable
     * @return Record
     */
    public static function make($data, bool $injectable = true): Record
    {
        return new static($data, $injectable);
    }

    /**
     * Convert data into a Record instance
     * @param $record
     * @return Record
     * @throws RunTimeError
     */
    public static function parse($record): Record
    {
        if ($record instanceof Record) {
            return $record;
        }
        if ($record instanceof stdClass) {
            $record = (array)$record;
        }
        if (is_array($record)) {
            return static::make($record);
        }
        $type = gettype($record);
        if ($type === TYPE_OBJECT) {
            $type = get_class($type);
        }
        throw new RunTimeError("Record must be an array or instanceof Record '{$type}' given");
    }

    /**
     * Get a value of Record
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->indexOf($name)) {
            return $this->get($name);
        }
        return null;
    }

    /**
     * Set a value in Record
     * @param string $name
     * @param $value
     * @return Record
     */
    public function __set($name, $value): Record
    {
        $this->set($name, $value);
        return $this;
    }

    /**
     * Get a value of the Record
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        return off($this->public, $name);
    }

    /**
     * Set a value of the Record
     * @param string $name
     * @param mixed $value
     * @return Record
     * @throws RunTimeError
     */
    public function set(string $name, $value): Record
    {
        if (!$this->isInjectable() && !$this->indexOf($name)) {
            throw new RunTimeError("The entry '{$name}' not exists");
        }
        $this->public[$name] = $value;
        return $this;
    }

    /**
     * Remove a key and associated value of the Record
     * @param string $name
     * @return Record
     */
    public function remove(string $name): Record
    {
        unset($this->public[$name]);
        return $this;
    }

    /**
     * Make a property hidden
     * @param string $name
     * @return Record
     */
    public function setPrivate(string $name): Record
    {
        if ($this->indexOf($name)) {
            $this->private[$name] = $this->public[$name];
            unset($this->public[$name]);
        }
        return $this;
    }

    /**
     * Make a property be public
     * @param string $name
     * @return Record
     */
    public function setPublic(string $name): Record
    {
        if ($this->indexOf($name, false)) {
            $this->public[$name] = $this->private[$name];
            unset($this->private[$name]);
        }
        return $this;
    }

    /**
     * This method merge an array of data to record overriding the previously value of the keys
     *
     * @param array $public
     * @param array $private
     * @return Record
     */
    public function merge(array $public, array $private = []): Record
    {
        $this->public = array_merge($this->public, $public);
        $this->private = array_merge($this->private, $private);
        return $this;
    }

    /**
     * This method import an array of data to record keeping the previously value of the keys
     *
     * @param array $public
     * @param array $private
     * @return Record
     */
    public function import(array $public, array $private = []): Record
    {
        $this->public = array_merge($public, $this->public);
        $this->private = array_merge($private, $this->private);
        return $this;
    }

    /**
     * Get the name of properties managed by Record
     * @param array $except
     * @return array
     */
    public function keys(array $except = []): array
    {
        return array_keys($this->all($except));
    }

    /**
     * Get the values of properties managed by Record
     * @param array $except
     * @return array
     */
    public function values(array $except = []): array
    {
        return array_values($this->all($except));
    }

    /**
     * Recover all values of the Record
     * @param array $except
     * @return array
     */
    public function all(array $except = []): array
    {
        $all = [];
        foreach ($this->public as $key => $value) {
            if (is_null($except) || !in_array($key, $except)) {
                $all[$key] = $value;
            }
        }
        return $all;
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * Check is exists a property into Record
     * @param string $name
     * @param bool $public
     * @return bool
     */
    public function indexOf(string $name, bool $public = true)
    {
        if ($public) {
            return isset($this->public[$name]);
        }
        return isset($this->private[$name]);
    }

    /**
     * Alias to indexOf
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return $this->indexOf($name);
    }

    /**
     * Return info about injectable property of the Record
     * @return bool
     */
    public function isInjectable(): bool
    {
        return $this->injectable;
    }

    /**
     * Check if the Record is empty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->public) === 0;
    }

    /**
     * Return the count of properties maneged by Record
     * @return int
     */
    public function size(): int
    {
        return count($this->public);
    }

    /**
     * Return a string of properties of the Record
     * @return string
     */
    public function toJSON(): string
    {
        return JSON::encode($this->public);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        $arrayObject = new \ArrayObject($this->public);

        return $arrayObject->getIterator();
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->public;
    }
}
