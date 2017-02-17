<?php

namespace Simples\Core\Data\Error;

use Simples\Core\Error\RunTimeError;

/**
 * Class ValidationError
 * @package Simples\Core\Data\Error
 */
class ValidationError extends RunTimeError
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
        parent::__construct(iif($message, 'Validation error'), $details);
    }
}
