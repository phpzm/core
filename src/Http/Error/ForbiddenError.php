<?php

namespace Simples\Core\Http\Error;

use Simples\Core\Error\RunTimeError;

/**
 * Class ForbiddenError
 * @package Simples\Core\Http\Error
 */
class ForbiddenError extends RunTimeError
{
    /**
     * @var int
     */
    protected $status = 403;
}
