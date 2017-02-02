<?php

namespace ${NAMESPACE}\Repository;

use ${NAMESPACE}\Model\${NAME};
use Simples\Core\Model\Repository\ApiRepository;

/**
 * Class ${NAME}Repository
 * @package ${NAMESPACE}\Repository
 */
class ${NAME}Repository extends ApiRepository
{
    /**
     * @var ${NAME}
     */
    protected $model;

    /**
     * ${NAME}Repository constructor.
     * @param ${NAME} $model
     */
    public function __construct(${NAME} $model)
    {
        parent::__construct($model);
    }
}
