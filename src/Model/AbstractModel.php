<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Persistence\Engine;
use ErrorException;
use Simples\Core\Route\Wrapper;

/**
 * Class AbstractModel
 * @package Simples\Core\Model
 */
abstract class AbstractModel extends Engine
{
    /**
     * @var string
     */
    protected $connection;

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
     * @var array
     */
    protected $createKeys = [
        'at' => '_created_at',
        'by' => '_created_by'
    ];

    /**
     * @var array
     */
    protected $updateKeys = [
        'at' => '_changed_at',
        'by' => '_changed_by'
    ];

    /**
     * @var mixed
     */
    protected $destroyKeys = [
        'at' => '_destroyed_at',
        'by' => '_destroyed_by'
    ];

    /**
     * AbstractModel constructor.
     * @param null $connection
     */
    public function __construct($connection = null)
    {
        parent::__construct($this->connection($connection));

        $this->addField($this->hashKey, 'string', ['validator' => 'unique']);
    }

    /**
     * @param $connection
     * @return mixed
     */
    protected function connection($connection)
    {
        if (!is_null($connection)) {
            $this->connection = $connection;
        } elseif (is_null($this->connection)) {
            $this->connection = env('DEFAULT_DATABASE');
        }
        return $this->connection;
    }

    /**
     * @param mixed $record
     * @return Record
     */
    abstract public function create($record = null);

    /**
     * @param mixed $record
     * @return Collection
     */
    abstract public function read($record = null);

    /**
     * @param mixed $record
     * @return Record
     */
    abstract public function update($record = null);

    /**
     * @param mixed $record
     * @return Record
     */
    abstract public function destroy($record = null);

    /**
     * @param $record
     * @return mixed
     */
    abstract public function fill($record);

    /**
     * @param $action
     * @param Record $record
     * @param Record $previous
     * @return bool
     */
    public function before($action, Record $record, Record $previous = null)
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
        if (off($options, 'pk')) {
            $this->primaryKey = $name;
        }
        $this->fields[$name] = [
            'name' => $name,
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
     * @param $action
     * @param Record $record
     * @return array
     */
    public function getValidators($action, Record $record)
    {
        $validators = [];
        foreach ($this->fields as $key => $field) {
            $validator = $this->getValidator($field, $action);
            if ($validator) {
                $validators[$key] = ['rules' => $validator, 'value' => $record->get($key)];
            }
        }
        return $validators;
    }

    /**
     * @param $field
     * @return array
     */
    private function getValidator($field, $action)
    {
        $rules = null;
        $validator = off(off($field, 'options'), 'validator');
        if ($validator) {
            $rules = [];
            $validators = $validator;
            if (!is_array($validators)) {
                $validators = [$validator];
            }
            foreach ($validators as $validator) {
                $options = null;
                switch ($validator) {
                    case 'unique':
                        // TODO: fix this to support unique on update
                        if ($action === Action::CREATE) {
                            $options = [
                                'class' => get_class($this),
                                'field' => $field['name']
                            ];
                        }
                        break;
                    default:
                        $options = [];
                        break;
                }
                if (!is_null($options)) {
                    $rules[$validator] = $options;
                }
            }
        }
        return $rules;
    }

    /**
     * @param $action
     * @param $record
     * @return array
     */
    public function getDefaults($action, $record = []): array
    {
        return [];
    }

    /**
     * @param $action
     * @return array
     */
    public function getFields($action)
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function hashKey()
    {
        return uniqid();
    }

    /**
     * @return mixed
     */
    public function getHashKey()
    {
        return $this->hashKey;
    }
}
