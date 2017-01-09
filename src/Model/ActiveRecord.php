<?php

namespace Simples\Core\Model;

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
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        return $this;
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return $this
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        if (!in_array($name, $this->fields)) {
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
     * @param Record $record
     * @return string
     */
    public final function create($record = null)
    {
        $action = __FUNCTION__;

        $record = $record ?? new Record($this->getValues());

        if ($this->before($action, $record)) {

            $created = $this
                ->table($this->collection)
                ->fields(array_keys($record->all()))
                ->insert(array_values($record->all()));

            $primaryKey = is_array($this->primaryKey) ? $this->primaryKey[0] : $this->primaryKey;

            $record->set($primaryKey, $created);

            if ($this->after($action, $record)) {
                return $created;
            }
        }
        return null;
    }

    public final function read($record = null)
    {

    }

    public final function save($record = null)
    {

    }

    public final function destroy($record = null)
    {

    }

}
