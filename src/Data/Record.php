<?php

namespace Simples\Core\Data;


use Simples\Core\Flow\Wrapper;
use Simples\Core\Helper\Json;

/**
 * Class Record
 * @property string json
 * @package Simples\Core\Domain
 */
class Record implements \IteratorAggregate
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
     * @var array
     */
    private $items;

    /**
     * Record constructor.
     * @param array $data
     * @param bool $injectable
     * @param array $items
     */
    public function __construct($data, $injectable = true, $items = [])
    {
        $this->public = (array)$data;
        $this->private = [];

        $this->injectable = $injectable;
        $this->items = $items;
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
     * @return bool|null|string
     */
    function __toString()
    {
        return $this->json;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        Wrapper::err('One Record can not be modified, you need "inject" new values in this case');
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
     * @param bool $private
     * @return mixed
     */
    public function get($name, $private = false)
    {
        $get = null;
        if ($this->indexOf($name)) {
            $get = $this->public[$name];
        } else if ($private) {
            $get = isset($this->private[$name]) ? $this->private[$name] : $get;
        }
        return $get;
    }

    /**
     * @param $name
     * @param $value
     * @return bool
     */
    public function set($name, $value)
    {
        if ($this->indexOf($name)) {
            $this->public[$name] = $value;
            return true;
        }
        return false;
    }

    /**
     * @param $name
     * @param $value
     * @return bool
     */
    public function inject($name, $value)
    {
        $injected = false;

        if ($this->injectable) {
            $this->public[$name] = $value;
            $injected = true;
        } else {
            Wrapper::err('This record is not "injectable"');
        }

        return $injected;
    }

    /**
     * @param $name
     */
    public function remove($name)
    {
        unset($this->public[$name]);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->public;
    }

    /**
     * @param $name
     * @return bool
     */
    public function indexOf($name)
    {
        if (is_array($this->public)) {
            return isset($this->public[$name]);
        }
        return false;
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
}