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
}
