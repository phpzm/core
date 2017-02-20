<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Error\SimplesValidationError;
use Simples\Core\Data\Record;
use Simples\Core\Error\SimplesRunTimeError;
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
     * @var array
     */
    private $maps = [];

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
     * @return $this
     */
    public static function box()
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
     * Configure the instance with reference properties
     * @param string $collection
     * @param string $primaryKey
     * @param string $relationship
     * @return $this
     * @throws SimplesRunTimeError
     */
    protected function instance(string $collection, string $primaryKey, string $relationship = '')
    {
        if ($this->collection) {
            $this->parents[$relationship] =  clone $this;
            $this->add($relationship)->integer()->setCollection($collection)->update(false);

            if (!$relationship) {
                throw new SimplesRunTimeError("When extending one model you need give a name to relationship");
            }
        }
        $this->collection = $collection;
        $this->primaryKey = $primaryKey;

        $this->add($this->hashKey)->hashKey();
        $this->add($primaryKey)->primaryKey();

        return $this;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @return Field
     */
    protected function add(string $name, string $type = '', array $options = []): Field
    {
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
     * @throws SimplesRunTimeError
     */
    protected function import(string $name, string $relationship, array $options = []): Field
    {
        $source = $this->get($relationship);
        $reference = $source->getReferences();

        $class = off($reference, 'class');
        if (!class_exists($class)) {
            throw new SimplesRunTimeError("Cant not import '{$name}' from '{$class}'");
        }

        /** @var DataMapper $class */
        $import = $class::box()->get($name);

        $from = new Field($import->getCollection(), $name, $import->getType(), $options);
        $this->fields[$name] = $from->from($source);

        return $from;
    }

    /**
     * @param string $name
     * @return Field
     */
    final public function get(string $name): Field
    {
        return off($this->fields, $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    final public function has(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * @param string $source
     * @param string $target
     */
    public function map(string $source, string $target)
    {
        $this->maps[$source] = $target;
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag");
     *
     * @param string $action
     * @param bool $strict
     * @return array
     */
    final public function getFields(string $action = '', bool $strict = true): array
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

        return $this->filterFields($this->getCollection(), $method, $strict);
    }

    /**
     * @param string $collection
     * @param string $method
     * @param bool $strict
     * @return array
     */
    private function filterFields(string $collection, string $method, bool $strict)
    {
        return array_filter($this->fields, function ($field) use ($collection, $method, $strict) {
            if ($strict && $field->getCollection() !== $collection) {
                return null;
            }
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
     * @param string $action
     * @param Record $record
     */
    public function configureFields(string $action, Record $record)
    {
        $action = ucfirst($action);
        if (method_exists($this, "configureFields{$action}")) {
            call_user_func_array([$this, "configureFields{$action}"], [$record]);
        }
        foreach ($this->maps as $source => $target) {
            $record->set($target, $record->get($source));
        }
    }

    /**
     * This method is called before the operation be executed, the changes made in Record will be save
     * @param string $action
     * @param Record $record
     * @param Record $previous
     * @return bool
     */
    protected function before(string $action, Record $record, Record $previous = null): bool
    {
        $action = ucfirst($action);
        if (method_exists($this, "before{$action}")) {
            return call_user_func_array([$this, "before{$action}"], [$record, $previous]);
        }
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
        $action = ucfirst($action);
        if (method_exists($this, "after{$action}")) {
            return call_user_func_array([$this, "after{$action}"], [$record]);
        }
        return true;
    }

    /**
     * @param string $action
     * @param Record $record
     * @return array
     */
    public function getDefaults(string $action, Record $record = null): array
    {
        $action = ucfirst($action);
        if (method_exists($this, "getDefaults{$action}")) {
            return call_user_func_array([$this, "getDefaults{$action}"], [$record]);
        }
        return [];
    }

    /**
     * @return string
     */
    final public function hashKey(): string
    {
        return uniqid();
    }

    /**
     * @return array
     */
    final public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * @return string
     */
    final public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    final public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    final public function getHashKey(): string
    {
        return $this->hashKey;
    }

    /**
     * @param $action
     * @throws SimplesRunTimeError
     */
    protected function throwAction($action)
    {
        throw new SimplesRunTimeError("Can't resolve '{$action}' in '" . get_class($this) . "'");
    }

    /**
     * @param $action
     * @param $hook
     * @throws SimplesRunTimeError
     */
    protected function throwHook($action, $hook)
    {
        throw new SimplesRunTimeError("Can't resolve hook `{$action}`.`{$hook}` in '" . get_class($this) . "'");
    }

    /**
     * @param array $details
     * @param string $message
     * @throws SimplesValidationError
     */
    protected function throwValidation(array $details, string $message = '')
    {
        throw new SimplesValidationError($details, $message);
    }
}
