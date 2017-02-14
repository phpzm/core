<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Data\Validation;
use Simples\Core\Error\RunTimeError;
use Simples\Core\Kernel\Container;
use Simples\Core\Persistence\Engine;

/**
 * Class AbstractModel
 * @package Simples\Core\Model
 */
abstract class AbstractModel extends Engine
{
    /**
     * Connection name
     * @var string
     */
    protected $connection;

    /**
     * Data source name
     * @var string
     */
    private $collection = '';

    /**
     * Collections parents created by extends
     * @var array
     */
    private $parents = [];

    /**
     * Fields of model
     * @var array
     */
    protected $fields = [];

    /**
     * Key used to relationships and to represent the primary key database
     * @var string
     */
    private $primaryKey = '';

    /**
     * Field with a unique hash what can be used to find a record and can be
     * created by client
     * @var string
     */
    protected $hashKey = '_id';

    /**
     * Fields to persist the creation object
     * @var array
     */
    protected $createKeys = [
        'at' => '_created_at',
        'by' => '_created_by'
    ];

    /**
     * Fields to persist details about the update
     * @var array
     */
    protected $updateKeys = [
        'at' => '_changed_at',
        'by' => '_changed_by'
    ];

    /**
     * Fields to persist details about the destroy
     * @var array
     */
    protected $destroyKeys = [
        'at' => '_destroyed_at',
        'by' => '_destroyed_by'
    ];

    /**
     * AbstractModel constructor to configure a new instance
     * @param string $connection (null)
     */
    public function __construct($connection = null)
    {
        parent::__construct($this->connection($connection));
        /*
        foreach (array_merge($this->createKeys, $this->updateKeys, $this->destroyKeys) as $type => $name) {
            $this->addField($name, $type === 'at' ? Field::TYPE_DATETIME : Field::TYPE_STRING);
        }
        */
    }

    /**
     * @return AbstractModel
     */
    public static function box(): AbstractModel
    {
        return Container::box()->make(static::class);
    }

    /**
     * Parse the connection name and choose a source to it
     * @param $connection
     * @return string
     */
    private function connection($connection): string
    {
        if (!is_null($connection)) {
            $this->connection = $connection;
        } elseif (is_null($this->connection)) {
            $this->connection = env('DEFAULT_DATABASE');
        }
        return $this->connection;
    }

    /**
     * Method with the responsibility of create a record of model
     * @param array|Record $record (null)
     * @return Record
     */
    abstract public function create($record = null): Record;

    /**
     * Read records with the filters informed
     * @param array|Record $record (null)
     * @return Collection
     */
    abstract public function read($record = null): Collection;

    /**
     * Update the record given
     * @param array|Record $record (null)
     * @return Record
     */
    abstract public function update($record = null): Record;

    /**
     * Remove the given record of database
     * @param array|Record $record (null)
     * @return Record
     */
    abstract public function destroy($record = null): Record;

    /**
     * Get total of records based on filters
     * @param array|Record $record (null)
     * @return int
     */
    abstract public function count($record = null): int;

    /**
     * This method is called before the operation be executed, the changes made in Record will be save
     * @param string $action
     * @param Record $record
     * @param Record $previous
     * @return bool
     */
    protected function before(string $action, Record $record, Record $previous = null): bool
    {
        return true;
    }

    /**
     * Triggered after operation be executed, the changes made in Record has no effect in storage
     * @param string $action
     * @param Record $record
     * @return bool
     */
    protected function after(string $action, Record $record): bool
    {
        return true;
    }

    /**
     * Configure the instance with reference properties
     * @param string $collection
     * @param string $primaryKey
     * @param string $hashKey
     * @return AbstractModel
     */
    protected function instance(string $collection, string $primaryKey, string $hashKey = ''): AbstractModel
    {
        if ($this->collection) {
            $this->parents[$this->collection] = $this->primaryKey;
        }
        $this->collection = $collection;
        $this->primaryKey = $primaryKey;
        $this->hashKey = $hashKey ? $hashKey : $this->hashKey;

        $this->field($this->hashKey, 'string')->validator('unique');
        return $this;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @return Field
     */
    protected function field(string $name, string $type = '', array $options = []): Field
    {
        if ($this->primaryKey === $name) {
            $options['primaryKey'] = true;
            $options['recover'] = false;
        } elseif (off($options, 'primaryKey')) {
            $this->primaryKey = $name;
        }
        $field = new Field($this->collection, $name, $type, $options);
        $this->fields[$name] = $field;

        return $field;
    }

    /**
     * Allow use this field like readonly in read filtering and getting it in record
     * @param string $name
     * @param string $relationship
     * @param array $options
     * @return Field
     * @throws RunTimeError
     */
    protected function import(string $name, string $relationship, array $options = []): Field
    {
        $source = $this->getField($relationship);
        $reference = $source->getReferences();

        $class = off($reference, 'class');
        if (!class_exists($class)) {
            throw new RunTimeError("Cant not import '{$name}' from '{$class}'");
        }

        /** @var DataMapper $class */
        $import = $class::box()->getField($name);

        $from = new Field($import->getCollection(), $name, $import->getType(), $options);
        $this->fields[$name] = $from->from($source);

        return $from;
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getHashKey(): string
    {
        return $this->hashKey;
    }

    /**
     * @param string $name
     * @return Field
     */
    public function getField(string $name): Field
    {
        return off($this->fields, $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * @param string $action
     * @param Record $record
     * @return array
     */
    public function getDefaults(string $action, Record $record = null): array
    {
        return [];
    }

    /**
     * @param string $action
     * @return array
     */
    public function getFields(string $action): array
    {
        $method = '';
        switch ($action) {
            case Action::CREATE: {
                $method = 'isCreate';
                break;
            }
            case Action::READ: {
                $method = 'isRead';
                break;
            }
            case Action::UPDATE: {
                $method = 'isUpdate';
                break;
            }
            case Action::RECOVER: {
                $method = 'isRecover';
                break;
            }
        }
        return array_filter($this->fields, function ($field) use ($method) {
            if (!$method) {
                return $field;
            }
            if ($method && $field->$method()) {
                return $field;
            }
            return null;
        });
    }

    /**
     * @return string
     */
    public function hashKey(): string
    {
        return uniqid();
    }

    /**
     * @param $record
     */
    public function dump($record)
    {
        return $record;
    }

    /**
     * @param string $action
     * @param Record $record
     * @return array
     */
    public function getValidators(string $action, Record $record): array
    {
        $validation = new Validation();
        foreach ($this->fields as $key => $field) {
            $validator = $this->getValidator($field, $action);
            if ($validator) {
                $validation->add($key, $record->get($key), $validator);
            }
        }
        return $validation->rules();
    }

    /**
     * @param Field $field
     * @param string $action
     * @return array|null
     */
    private function getValidator(Field $field, string $action)
    {
        $rules = null;
        $validators = $field->getValidators();
        if ($validators) {
            $rules = [];
            foreach ($validators as $validator => $options) {
                if (!$options) {
                    $options = [];
                }
                if (!is_array($options)) {
                    $options = [$options];
                }
                switch ($validator) {
                    case 'unique':
                        // TODO: fix this to support unique on update
                        if ($action === Action::CREATE) {
                            $options = array_merge($options, [
                                'class' => get_class($this),
                                'field' => $field->getName()
                            ]);
                        }
                        break;
                }
                if (count($field->getEnum())) {
                    $options = array_merge($options, [
                        'enum' => $field->getEnum()
                    ]);
                }
                $rules[$validator] = $options;
            }
        }
        return $rules;
    }
}
