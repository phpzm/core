<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Persistence\Engine;
use ErrorException;

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
     * @var array
     */
    protected $belongsTo = [];


    /**
     * @var array
     */
    protected $hasMany = [];

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
     * @param $name
     * @param $type
     * @param $options
     * @return $this
     */
    public function addField($name, $type, $options = [])
    {
        $default = [
            'label' => '', 'validator' => '', 'create' => true, 'read' => true, 'update' => true
        ];
        $options = array_merge($default, $options);
        $validators = off($options, 'validator');
        if ($validators) {
            if (!is_array($validators)) {
                $validators = [$validators];
            }
            foreach ($validators as $key => $validator) {
                switch ($validator) {
                    case 'unique':
                        $validators[$key] = 'unique:' . get_class($this) . ',' . $name;
                        break;
                }
            }
            $options['validator'] = $validators;
        }
        $this->fields[$name] = [
            'type' => $type,
            'options' => $options
        ];
        $relation = off($options, 'relation');
        if ($relation) {
            $this->addRelation($name, off($relation, 'type'), off($relation, 'class'), off($relation, 'source'));
        }
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
     * @param $target
     * @param $type
     * @param $class
     * @param $source
     * @return $this
     * @throws ErrorException
     */
    public function addRelation($target, $type, $class, $source)
    {
        if (!class_exists($class)) {
            $current = get_class($this);
            throw new ErrorException("Cant resolve the relationship between '{$class}' in '{$current}'");
        }
        $this->$type[] = new Relation($target, $class, $source);
        return $this;
    }

    /**
     * @param $class
     * @param $source
     * @param null $target
     * @return mixed
     */
    public function belongsTo($class, $source, $target = null)
    {
        $type = 'belongsTo';
        if ($target) {
            return $this->addRelation($target, $type, $class, $source);
        }
        return $this->relation($type, $class, $source);
    }

    /**
     * @param $class
     * @param $source
     * @param null $target
     * @return mixed
     */
    public function hasMany($class, $source, $target = null)
    {
        $type = 'hasMany';
        if ($target) {
            return $this->addRelation($target, $type, $class, $source);
        }
        return $this->relation($type, $class, $source);
    }

    /**
     * @param $type
     * @param $class
     * @param $field
     * @return array
     */
    public function relation($type, $class, $field)
    {
        return ['type' => $type, 'class' => $class, 'source' => $field];
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
     * @return array
     */
    public function getValidators()
    {
        $validators = [];
        foreach ($this->fields as $name => $field) {
            $validator = off(off($field, 'options'), 'validator');
            if ($validator) {
                $validators[$name] = $validator;
            }
        }
        return $validators;
    }

    /**
     * @param $action
     * @param $record
     * @return Record|array
     */
    public function getDefaults($action, $record = []): array
    {
        return [];
    }

}