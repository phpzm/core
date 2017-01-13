<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Database\Engine;

/**
 * Class AbstractModel
 * @package Simples\Core\Model
 */
abstract class AbstractModel extends Engine
{
    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var string
     */
    protected $collection = '';

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var mixed
     */
    protected $primaryKey = '';

    /**
     * @var mixed
     */
    protected $hashKey = '_id';

    /**
     * @var mixed
     */
    protected $deletedKey = '_trash';

    /**
     * @var array
     */
    protected $timestampsKeys = [
        'created' => [
            'at' => '_created_at',
            'by' => '_created_by'
        ],
        'saved' => [
            'at' => '_changed_at',
            'by' => '_changed_by'
        ]
    ];

    /**
     * @var bool
     */
    public $log = false;

    /**
     * @param $name
     * @param $type
     * @param $options
     * @return $this
     */
    public function addField($name, $type, $options = [])
    {
        $default = [
            'label' => '', 'create' => true, 'read' => true, 'update' => true
        ];
        $this->fields[$name] = [
            'type' => $type,
            'options' => array_merge($default, $options)
        ];
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getField($name)
    {
        return off($this->fields, $name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasField($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * @param $class
     * @param $source
     * @param $target
     * @return $this
     */
    public function addForeign($class, $source, $target)
    {
        return $this;
    }

    /**
     * @param $class
     * @param $field
     * @return array
     */
    public function foreign($class, $field)
    {
        return [$class => $field];
    }

    /**
     * @param $rule
     * @param $parameters
     * @return array
     */
    public function validator($rule, ...$parameters)
    {
        if (count($parameters)) {
            return [$rule => $parameters];
        }
        return $rule;
    }

    /**
     * @param $record
     * @return mixed
     */
    public abstract function fill($record);

    /**
     * @param $action
     * @param Record $record
     * @return bool
     */
    public function before($action, Record $record)
    {
        return true;
    }

    /**
     * @param $action
     * @param Record $record
     * @return bool
     */
    public function after($action, Record $record)
    {
        return true;
    }

    /**
     * @param mixed $record
     * @return Record
     */
    public abstract function create($record = null);

    /**
     * @param mixed $record
     * @return Collection
     */
    public abstract function read($record = null);

    /**
     * @param mixed $record
     * @return Record
     */
    public abstract function update($record = null);

    /**
     * @param mixed $record
     * @return Record
     */
    public abstract function destroy($record = null);

}