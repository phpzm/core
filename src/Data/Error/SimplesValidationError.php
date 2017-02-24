<?php

namespace Simples\Core\Data\Error;

use Simples\Core\Error\SimplesRunTimeError;

/**
 * Class ValidationError
 * @package Simples\Core\Data\Error
 */
class SimplesValidationError extends SimplesRunTimeError
{
    /**
     * @var int
     */
    protected $status = 400;

    /**
     * ValidationError constructor.
     * @param array $details
     * @param string $message
     */
    public function __construct(array $details = [], string $message = '')
    {
        parent::__construct('Validation error' . ($message ? 'in `' . $message . '`' : ''), $details);
    }
}
