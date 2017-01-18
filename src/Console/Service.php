<?php

namespace Simples\Core\Console;

use Simples\Core\Kernel\App;

/**
 * Class Service
 * @package Simples\Core\Console
 */
abstract class Service
{
    /**
     * @var array
     */
    const KILLERS = ['exit', 'q', 'quit', 'bye'];

    /**
     * @param App $app
     */
    abstract public static function execute(App $app);
}
