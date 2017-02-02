<?php
/**
 * Created by Simples Creator Engine - SCE.
 */

namespace ${NAMESPACE}\Controller;


use ${NAMESPACE}\Repository\${NAME}Repository;
use Simples\Core\Http\Specialty\ApiController;

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