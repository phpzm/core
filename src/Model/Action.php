<?php

namespace Simples\Core\Model;

/**
 * Class Action
 * @package Simples\Core\Model
 */
abstract class Action
{
    /**
     * @var string
     */
    const
        CREATE = 'create', READ = 'read', UPDATE = 'update', DESTROY = 'destroy', RECOVER = 'recover';
}
