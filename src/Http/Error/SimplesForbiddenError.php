<?php

namespace Simples\Core\Http\Error;

use Simples\Core\Error\SimplesRunTimeError;

/**
 * Class ForbiddenError
 * @package Simples\Core\Http\Error
 */
class SimplesForbiddenError extends SimplesRunTimeError
{
    /**
     * @var int
     */
    protected $status = 403;
}
