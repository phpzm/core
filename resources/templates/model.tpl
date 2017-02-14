<?php

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

        $this->instance('${table}', '${primaryKey}');

        $this->field('${primaryKey}')->int();
        $this->field('${description}')->string()->required();
    }

    /**
    * @param string $action
    * @param Record $record
    * @param Record|null $previous
    * @return bool
    */
    protected function before(string $action, Record $record, Record $previous = null): bool
    {
        return parent::before($action, $record, $previous);
    }

    /**
     * @param string $action
     * @param Record $record
     * @return bool
     */
    protected function after(string $action, Record $record): bool
    {
        return parent::after($action, $record);
    }
}
