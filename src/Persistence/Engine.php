<?php

namespace Simples\Core\Persistence;

use Simples\Core\Helper\Json;
use Simples\Core\Helper\Text;
use Simples\Core\Kernel\App;

/**
 * Class Engine
 * @package Simples\Core\Persistence
 *
 * @method $this collection (string $table)
 * @method $this join (string $join)
 * @method $this fields (array $fields)
 * @method $this where (array $where)
 * @method $this order (array $order)
 * @method $this group (array $group)
 * @method $this having (array $having)
 * @method $this limit (array $join)
 *
 * @method $this log (bool $active)
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
     * @var array|mixed
     */
    private $settings = [];

    /**
     * Engine constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->settings = off(App::config('database'), $id);
        if ($this->settings) {
            $this->driver = Factory::create($this->settings);
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
     * @return SQLDriver
     * @throws \Exception
     */
    protected function driver()
    {
        if ($this->driver) {
            return $this->driver;
        }
        $settings = Text::replace(Json::encode($this->settings), '"', "'");
        throw new \Exception("Cant use driver: {$settings}");
    }

    /**
     * @param array $values
     * @return string
     */
    public function add($values)
    {
        return $this->driver()->create($this->clausules, $values);
    }

    /**
     * @param $values
     * @return string
     */
    final public function get($values = [])
    {
        return $this->driver()->read($this->clausules, $values);
    }

    /**
     * @param $values
     * @param $filters
     * @return int
     */
    public function set($values, $filters = [])
    {
        return $this->driver()->update($this->clausules, $values, $filters);
    }

    /**
     * @param $filters
     * @return int
     */
    public function remove($filters)
    {
        return $this->driver()->destroy($this->clausules, $filters);
    }
}
