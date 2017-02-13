<?php

namespace Simples\Core\Data;

use Simples\Core\Error\RunTimeError;
use Simples\Core\Helper\Json;
use Simples\Core\Unit\Origin;
use IteratorAggregate;
use JsonSerializable;
use stdClass;

/**
 * Class Record
 * @property string json
 * @package Simples\Core\Domain
 */
class Record extends Origin implements IteratorAggregate, JsonSerializable
{
    /**
     * @var array
     */
    private $public;

    /**
     * @var array
     */
    private $private;

    /**
     * @var bool
     */
    private $injectable;

    /**
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
     * @param $name
     * @return bool|null|string
     */
    public function __get($name)
    {
        $value = null;

        switch ($name) {
            case 'json':
                $value = Json::encode($this->public);
                break;
            default:
                if ($this->indexOf($name)) {
                    $value = $this->public[$name];
                }
                break;
        }

        return $value;
    }

    /**
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
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        return off($this->public, $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Record
     * @throws RunTimeError
     */
    public function set(string $name, $value): Record
    {
        if (!$this->isInjectable() && !$this->indexOf($name) ) {
            throw new RunTimeError("The entry '{$name}' not exists");
        }
        $this->public[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return Record
     */
    public function remove(string $name): Record
    {
        unset($this->public[$name]);
        return $this;
    }

    /**
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
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return $this->indexOf($name);
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
     * @return bool
     */
    public function isInjectable(): bool
    {
        return $this->injectable;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->public) === 0;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return count($this->public);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->json;
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
