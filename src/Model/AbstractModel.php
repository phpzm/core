<?php

namespace Simples\Core\Model;

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
     * @var array
     */
    protected $fields = [];

    /**
     * @var mixed
     */
    protected $primaryKey = 'id';

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
     * @param $name
     * @param $type
     * @param $label
     * @param $options
     * @return $this
     */
    public function add($name, $type, $label, $options = [])
    {
        $default = [
            'insert' => true, 'read' => true, 'update' => true
        ];
        $this->fields[$name] = [
            'type' => $type,
            'label' => $label,
            'options' => array_merge($default, $options)
        ];
        return $this;
    }

    /**
     * @param null $record
     * @return mixed
     */
    public abstract function create($record = null);

    /**
     * @param null $record
     * @return mixed
     */
    public abstract function read($record = null);

    /**
     * @param null $record
     * @return mixed
     */
    public abstract function save($record = null);

    /**
     * @param null $record
     * @return mixed
     */
    public abstract function destroy($record = null);

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

}