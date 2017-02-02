<?php

/**
 * Created by Simples Creator Engine - SCE.
 */

namespace ${NAMESPACE}\Model;

use Simples\Core\Data\Record;
use Simples\Core\Model\DataMapper;

/**
 * Class ${NAME}
 * @package ${NAMESPACE}\Model
 */
class ${NAME} extends DataMapper
{
    /**
     * ${NAME} constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->collection = 'table';
        $this->primaryKey = 'field'; // optional $primaryKey is overwritten by ['pk' => true]

        $this->addField('field', 'int', ['pk' => true]);
        $this->addField('field', 'string', ['validator' => 'required']);
    }

    /**
    * @param $action
    * @param Record $record
    * @param Record|null $previous
    * @return bool
    */
    public function before($action, Record $record, Record $previous = null)
    {
        return parent::before($action, $record, $previous);
    }

    /**
     * @param $action
     * @param Record $record
     * @return bool
     */
    public function after($action, Record $record)
    {
        return parent::after($action, $record);
    }
}
