<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Data\Validation;
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

        $this->addField($this->hashKey, 'string')->validator('unique');
        /*
        foreach (array_merge($this->createKeys, $this->updateKeys, $this->destroyKeys) as $type => $name) {
            $this->addField($name, $type === 'at' ? Field::TYPE_DATETIME : Field::TYPE_STRING);
        }
        */
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
     * @param mixed $record
     * @return Record
     */
    abstract public function create($record = null);

    /**
     * Read records with the filters informed
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
     * @param Record|null $record
     * @return int
     */
    abstract public function count(Record $record = null) : int;

    /**
     * @param string $action
     * @param Record $record
     * @param Record $previous
     * @return bool
     */
    public function before(string $action, Record $record, Record $previous = null): bool
    {
        return true;
    }

    /**
     * @param string $action
     * @param Record $record
     * @return bool
     */
    public function after(string $action, Record $record): bool
    {
        return true;
    }

    /**
     * @param string $collection
     * @param string $primaryKey
     * @return AbstractModel
     */
    public function collection(string $collection, string $primaryKey): AbstractModel
    {
		if ($this->collection) {
            $this->parents[$this->collection] = $this->primaryKey;
        }
        $this->collection = $collection;
        $this->primaryKey = $primaryKey;
        return $this;
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
     * @param string $name
     * @param string $type
     * @param array $options
     * @return Field
     */
    public function addField(string $name, string $type, array $options = []): Field
    {
        if ($this->primaryKey === $name) {
            $options['primaryKey'] = true;
        } else if (off($options, 'primaryKey')) {
            $this->primaryKey = $name;
        }
        $field = new Field($this->collection, $name, $type, $options);
        $this->fields[$name] = $field;

        return $field;
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
                switch ($validator) {
                    case 'unique':
                        // TODO: fix this to support unique on update
                        if ($action === Action::CREATE) {
                            $options = [
                                'class' => get_class($this),
                                'field' => $field->getName()
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
     * @param string $action
     * @param Record $record
     * @return array
     */
    public function getDefaults(string $action, Record $record = null): array
    {
        return [];
    }

    /**
     * @param $action
     * @return array
     */
    public function getFields(string $action): array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function hashKey(): string
    {
        return uniqid();
    }

    /**
     * @return string
     */
    public function getHashKey(): string
    {
        return $this->hashKey;
    }
}
