<?php

namespace Simples\Core;

use Simples\Core\Gateway\Request;
use Simples\Core\Gateway\Response;
use Simples\Core\Flow\Router;

/**
 * Class App
 * @package Core
 */
class App
{
    /**
     * @var Request
     */
    private static $REQUEST = null;

    /**
     * @var Response
     */
    private static $RESPONSE = null;

    /**
     * @var array
     */
    private static $CONFIGS = [];

    /**
     * @return mixed
     */
    public static function output()
    {
        //ob_start();

        $router = new Router(self::request(), self::response());

        $run = self::routes($router)->run();

        //ob_end_clean();

        return $run;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(self::request(), $name)) {

            return self::request()->$name();

        } else if (method_exists(self::response(), $name)) {

            return self::response()->$name();
        }

        return null;
    }

    /**
     * @return Request
     */
    public static function request()
    {
        if (!self::$REQUEST) {
            self::$REQUEST = new Request();
        }
        return self::$REQUEST;
    }

    /**
     * @return Response
     */
    public static function response()
    {
        if (!self::$RESPONSE) {
            self::$RESPONSE = new Response();
        }
        return self::$RESPONSE;
    }

    /**
     * @param $name
     * @return object
     */
    public static function config($name)
    {
        if (isset(self::$CONFIGS[$name])) {
            return self::$CONFIGS[$name];
        }

        $config = [];
        $filename = path(true, 'app', 'configs', $name . '.php');
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            $config = require $filename;
        }
        self::$CONFIGS[$name] = (object)$config;

        return self::$CONFIGS[$name];
    }

    /**
     * @param Router $router
     * @param array $files
     * @return Router
     */
    public static function routes(Router $router, array $files = null)
    {
        $files = $files ? $files : self::config('route')->files;

        foreach ($files as $file) {

            $filename = __APP_ROOT__ . '/' . $file;
            if (file_exists($filename)) {

                /** @noinspection PhpIncludeInspection */
                $callable = require_once $filename;
                if (is_callable($callable)) {
                    $callable($router);
                }
            }
        }

        return $router;
    }

    /**
     * @param $uri
     * @param bool $print
     * @return string
     */
    public static function route($uri, $print = true)
    {
        $route = '//' . self::request()->getUrl() . '/' . ($uri{0} === '/' ? substr($uri, 1) : $uri);
        if ($print) {
            out($route);
        }
        return $route;
    }

    /**
     * @param $output
     */
    public static function headers($output)
    {
        if (method_exists($output, 'getHeaders')) {

            $headers = $output->getHeaders();

            foreach ($headers as $header) {
                header($header->string, $header->replace, $header->code);
            }
        }
    }

    /**
     * @param $output
     */
    public static function http($output)
    {
        if (method_exists($output, 'getBody')) {
            $output = $output->getBody();
        }
        out($output);
    }

    /**
     * @param $output
     */
    public static function cli($output)
    {
        if (method_exists($output, 'getBody')) {
            $output = $output->getBody();
        }
        out(
            '|--------------------------------------------------------------------------|' . PHP_EOL .
            '| Simples CLI Interface                                                    |' . PHP_EOL .
            '|--------------------------------------------------------------------------|' . PHP_EOL .
            '| -uri=/migration                                                          |' . PHP_EOL .
            '| -uri=/clear                                                              |'
        );
        echo PHP_EOL;
        out('|--------------------------------------------------------------------------|' . PHP_EOL);
        echo PHP_EOL;
        out($output);
        echo PHP_EOL;
        out('---------------------------------------------------------------------------|' . PHP_EOL);
        echo PHP_EOL;
    }

}