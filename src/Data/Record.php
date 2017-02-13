<?php

namespace Simples\Core\Data;

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
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        // silent
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return off($this->public, $name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function has($name)
    {
        return isset($this->public[$name]);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->public[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     */
    public function remove($name)
    {
        unset($this->public[$name]);
    }

    /**
     * @param $name
     */
    public function setPrivate($name)
    {
        if ($this->indexOf($name)) {
            $this->private[$name] = $this->public[$name];
            unset($this->public[$name]);
        }
    }

    /**
     * @param $name
     */
    public function setPublic($name)
    {
        if ($this->indexOf($name, false)) {
            $this->public[$name] = $this->private[$name];
            unset($this->private[$name]);
        }
    }

    /**
     * @param array $except
     * @return array
     */
    public function all($except = null)
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
     * @param $name
     * @param bool $public
     * @return bool
     */
    public function indexOf($name, $public = true)
    {
        if ($public) {
            return isset($this->public[$name]);
        }
        return isset($this->private[$name]);
    }

    /**
     * @param $items
     * @return bool
     */
    public function items($items)
    {
        if (!$this->items) {
            return $this->items = $items;
        }
        return false;
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
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param bool $injectable
     */
    public function setInjectable($injectable)
    {
        $this->injectable = $injectable;
    }

    /**
     * @return bool
     */
    public function isInjectable()
    {
        return $this->injectable;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->public) === 0;
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->public);
    }

    /**
     * @return bool|null|string
     */
    public function __toString()
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
