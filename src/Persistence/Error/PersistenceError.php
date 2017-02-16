<?php

namespace Simples\Core\Persistence\Error;

use Simples\Core\Error\RunTimeError;

/**
 * Class PersistenceError
 * @package Simples\Core\Persistence\Error
 */
class PersistenceError extends RunTimeError
{
    /**
     * @var int
     */
    protected $status = 412;

    /**
     * PersistenceError constructor.
     * @param array $details
     * @param array $context
     */
    public function __construct(array $details = [], array $context = [])
    {
        parent::__construct('Persistence error', $details, $context);
    }
}
