<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;

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
     * @param mixed $record
     * @return Record
     */
    public final function create($record = null)
    {
        $action = __FUNCTION__;

        $record = new Record($this->getValues());

        if ($this->before($action, $record)) {

            $created = $this
                ->collection($this->collection)
                ->fields(array_keys($record->all()))
                ->add(array_values($record->all()));

            $primaryKey = is_array($this->primaryKey) ? $this->primaryKey[0] : $this->primaryKey;

            $record->set($primaryKey, $created);

            if ($this->after($action, $record)) {
                return $record;
            }
        }
        return null;
    }

    /**
     * @param mixed $record
     * @return Collection
     */
    public function read($record = null)
    {
        $array = $this
            ->collection($this->collection)
            ->fields('*')
            ->get();

        return new Collection($array);
    }

    /**
     * @param mixed $record
     * @return Record
     */
    public function update($record = null)
    {
        // set
        return new Record([]);
    }

    /**
     * @param mixed $record
     * @return Record
     */
    public function destroy($record = null)
    {
        // remove
        return new Record([]);
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
            $this->$field = $value;
        }
        return true;
    }

}
