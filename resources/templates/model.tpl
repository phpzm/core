<?php

namespace ${NAMESPACE};

use Simples\Core\Data\Record;
use Simples\Core\Model\DataMapper;

/**
 * Class ${NAME}
 * @package ${NAMESPACE}
 */
class ${NAME} extends DataMapper
{
    /**
     * Conta constructor.
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
     * @return bool
     */
    public function before($action, Record $record)
    {
        return parent::before($action, $record);
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