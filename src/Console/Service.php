<?php

namespace Simples\Core\Console;

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
     * @param $app
     */
    public static abstract function execute($app);
}