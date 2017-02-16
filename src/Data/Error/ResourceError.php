<?php

namespace Simples\Core\Data\Error;

use Simples\Core\Error\RunTimeError;

/**
 * Class ResourceError
 * @package Simples\Core\Data\Error
 */
class ResourceError extends RunTimeError
{
    /**
     * @var int
     */
    protected $status = 410;

    /**
     * ResourceError constructor.
     * @param array $details
     */
    public function __construct(array $details = [])
    {
        parent::__construct('Resource error', $details);
    }
}
