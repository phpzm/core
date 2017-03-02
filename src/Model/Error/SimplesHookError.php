<?php

namespace Simples\Core\Model\Error;

use Simples\Core\Error\SimplesRunTimeError;

/**
 * Class SimplesHookError
 * @package Simples\Core\Model\Error
 */
class SimplesHookError extends SimplesRunTimeError
{
    /**
     * SimplesHookError constructor.
     * @param string $class
     * @param string $action
     * @param string $hook
     */
    public function __construct(string $class, string $action, string $hook)
    {
        parent::__construct("Can't resolve hook `{$action}`.`{$hook}` in '" . $class . "'");
    }
}
