<?php

namespace ${NAMESPACE}\Controller;

use ${NAMESPACE}\Repository\${NAME}Repository;
use Simples\Core\Http\Specialty\ApiController;

/**
 * Class ${NAME}
 * @package ${NAMESPACE}\Controller
 */
class ${NAME}Controller extends ApiController
{
    /**
     * @var ${NAME}Repository
     */
    protected $repository;

    /**
     * ${NAME}Controller constructor.
     * @param ${NAME}Repository $repository
     */
    public function __construct(${NAME}Repository $repository)
    {
        $this->repository = $repository;
    }
}
