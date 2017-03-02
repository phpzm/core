<?php

namespace Simples\Core\Model\Error;

use Simples\Core\Error\SimplesRunTimeError;

/**
 * Class SimplesHookError
 * @package Simples\Core\Model\Error
 */
class SimplesActionError extends SimplesRunTimeError
{
    /**
     * SimplesActionError constructor.
     * @param string $class
     * @param string $action
     */
    public function __construct(string $class, string $action)
    {
        parent::__construct("Can't resolve '{$action}' in '" . $class . "'");
    }
}
