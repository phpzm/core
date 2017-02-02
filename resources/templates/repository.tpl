<?php
/**
 * Created by Simples Creator Engine - SCE.
 */

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
     * ${NAME}Repository constructor.
     * @param ${NAME} $object
     */
    public function __construct(${NAME} $object)
    {
        parent::__construct($object);
    }

}