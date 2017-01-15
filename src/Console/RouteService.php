<?php

namespace Simples\Core\Console;

use Simples\Core\Route\Router;
use Simples\Core\Kernel\App;

/**
 * Class RouteService
 * @package Simples\Core\Console
 */
abstract class RouteService
{
    /**
     * @param $app
     */
    public static function execute($app)
    {
        $routes = App::routes(new Router())->getTrace();

        echo str_pad('METHOD', 10, ' '), ' | ', str_pad('URI', 50, ' '), ' | ', str_pad('GROUP', 6, ' '), PHP_EOL,
        str_pad('', 140, '-'), PHP_EOL;

        foreach ($routes as $route) {
            echo
            str_pad($route['method'], 10, ' '), ' | ',
            str_pad($route['uri'], 50, ' '), ' | ',
            str_pad(!!off($route['options'], 'group'), 6, ' '), ' | ', $route['callback'], PHP_EOL;
        }
    }
}