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
    const CREATE = 'create';

    /**
     * @var string
     */
    const READ = 'read';

    /**
     * @var string
     */
    const UPDATE = 'update';

    /**
     * @var string
     */
    const DESTROY = 'destroy';
}
