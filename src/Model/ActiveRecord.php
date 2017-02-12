<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Exception;

/**
 * Class ActiveRecord
 * @package Simples\Core\Model
 */
class ActiveRecord extends AbstractModel
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * ActiveRecord constructor.
     */
    public function __construct()
    {
        parent::__construct($this->connection);

        throw new Exception('Needs review');
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     * @return $this|null
     */
    public function __set($name, $value)
    {
        if (!$this->hasField($name)) {
            return null;
        }
        $this->values[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Method with the responsibility of create a record of model
     * @param array|Record $record (null)
     * @return Record
     */
    final public function create($record = null): Record
    {
        return null;
    }

    /**
     * Read records with the filters informed
     * @param array|Record $record (null)
     * @return Collection
     */
    public function read($record = null): Collection
    {
        return new Collection([]);
    }

    /**
     * Update the record given
     * @param array|Record $record (null)
     * @return Record
     */
    public function update($record = null): Record
    {
        return new Record([]);
    }

    /**
     * Remove the given record of database
     * @param array|Record $record (null)
     * @return Record
     */
    public function destroy($record = null): Record
    {
        return new Record([]);
    }

    /**
     * Get total of records based on filters
     * @param array|Record $record (null)
     * @return int
     */
    public function count($record = null): int
    {
        return 0;
    }

    /**
     * @param $record
     * @return bool
     */
    public function fill($record)
    {
        if (!is_iterator($record)) {
            return false;
        }
        foreach ($record as $field => $value) {
            /** @noinspection PhpVariableVariableInspection */
            $this->$field = $value;
        }
        return true;
    }
}
