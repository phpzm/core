<?php

namespace Simples\Core\Kernel;

use Simples\Core\Console\Service;
use Simples\Core\Console\ControllerService;
use Simples\Core\Console\HelpService;
use Simples\Core\Console\ModelService;
use Simples\Core\Console\RepositoryService;
use Simples\Core\Console\RouteService;

/**
 * Class Console
 * @package Simples\Core\Kernel
 */
class Console
{
    /**
     * @var array
     */
    private static $services = [];

    /**
     * @var string
     */
    private static $otherWise = '';

    /**
     *
     */
    protected static function boot()
    {
        static::register('route', function ($app, $parameters) {
            RouteService::execute($app, $parameters);
        });
        static::register('model', function ($app, $parameters) {
            ModelService::execute($app, $parameters);
        });
        static::register('controller', function ($app, $parameters) {
            ControllerService::execute($app, $parameters);
        });
        static::register('repository', function ($app, $parameters) {
            RepositoryService::execute($app, $parameters);
        });
        static::register('help', function ($app, $parameters) {
            HelpService::execute($app, $parameters);
        });
        static::otherWise('help');
    }

    /**
     * @param App $app
     * @param array $parameters
     */
    public static function handler(App $app, array $parameters)
    {
        static::boot();

        echo "@start/\n";
        echo "Press ^C or type 'exit' at any time to quit.\n";

        $service = off($parameters, 0, static::$otherWise);
        array_shift($parameters);
        do {
            static::execute($app, $service, $parameters);
            $service = read();
        } while (!in_array($service, Service::KILLERS));
    }

    /**
     * @param string $id
     * @param callable $callable
     */
    private static function register(string $id, callable $callable)
    {
        static::$services[$id] = $callable;
    }

    /**
     * @param App $app
     * @param string $service
     * @param array $parameters
     */
    private static function execute(App $app, string $service, array $parameters)
    {
        if (isset(static::$services[$service])) {
            call_user_func_array(static::$services[$service], [$app, $parameters]);
        }
    }

    /**
     * @param $otherWise
     */
    private static function otherWise($otherWise)
    {
        static::$otherWise = $otherWise;
    }
}
