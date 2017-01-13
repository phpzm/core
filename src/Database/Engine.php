<?php

namespace Simples\Core\Database;

use Simples\Core\Kernel\App;

/**
 * Class Engine
 * @package Simples\Core\Database
 *
 * @method Engine collection (string $table)
 * @method Engine join (string $join)
 * @method Engine fields (array $fields)
 * @method Engine where (array $where)
 * @method Engine order (array $order)
 * @method Engine group (array $group)
 * @method Engine having (array $having)
 * @method Engine limit (array $join)
 *
 * @method Engine log (bool $active)
 */
class Engine
{
    /**
     * @var SQLDriver
     */
    private $driver;

    /**
     * @var array
     */
    private $clausules = [];

    /**
     * Engine constructor.
     * @param $id
     * @param string $hashKey
     * @param string $deletedKey
     * @param array $timestampsKeys
     */
    public function __construct($id, $hashKey = '', $deletedKey = '', array $timestampsKeys = [])
    {
        $settings = off(App::config('database'), $id);
        if ($settings) {
            $this->driver = Factory::create($settings, $hashKey, $deletedKey, $timestampsKeys);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $clausule = $arguments[0];
        if (count($arguments) > 1) {
            $clausule = $arguments;
        }

        $this->clausules[strtolower($name)] = $clausule;

        return $this;
    }

    /**
     * @param array $values
     * @return string
     */
    public function add($values)
    {
        return $this->driver->create($this->clausules, $values);
    }

    /**
     * @param $values
     * @return string
     */
    public final function get($values = [])
    {
        return $this->driver->read($this->clausules, $values);
    }

    /**
     * @param $values
     * @param $filters
     * @return int
     */
    public function set($values, $filters = [])
    {
        return $this->driver->update($this->clausules, $values, $filters);
    }

    /**
     * @param $filters
     * @return int
     */
    public function remove($filters)
    {
        return $this->driver->destroy($this->clausules, $filters);
    }

}