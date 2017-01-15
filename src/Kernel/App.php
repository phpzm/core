<?php

namespace Simples\Core\Kernel;

use Simples\Core\Console\RouteService;
use Simples\Core\Route\Router;
use Simples\Core\Http\Request;
use Simples\Core\Http\Response;

/**
 * Class App
 * @package Core\Kernel
 */
class App
{
    /**
     * @var Request
     */
    private static $REQUEST = null;

    /**
     * @var array
     */
    private static $CONFIGS = [];

    /**
     * @var string
     */
    public static $ROOT;

    /**
     * @var array
     */
    private static $options;

    /**
     * App constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $default = [
            'root' => dirname(dirname(dirname(dirname(dirname(__DIR__))))),
            'lang' => [
                'default' => 'en', 'fallback' => 'en'
            ],
            'labels' => true,
            'separator' => '@',
            'strict' => false
        ];
        self::$options = array_merge($default, $options);

        self::$ROOT = off(self::$options, 'root');
    }

    /**
     * @param null $key
     * @param null $value
     * @return array
     */
    public static function options($key = null, $value = null)
    {
        if ($key) {
            if (!$value) {
                return self::$options[$key];
            }
            self::$options[$key] = $value;
        }
        return self::$options;
    }

    /**
     * @param bool $print
     * @return mixed
     */
    public function http($print = true)
    {
        try {
            $response = $this->handlerHttp();

            if ($print) {

                $headers = $response->getHeaders();
                foreach ($headers as $name => $value) {
                    header(implode(':', [$name, $value]), true);
                }

                http_response_code($response->getStatusCode());

                $contents = $response->getBody()->getContents();
                if ($contents) {
                    out($contents);
                }
            }

            return $response;
        }
        catch (\ErrorException $error) {
            echo  "ErrorException: '", error_message($error);
        }
        return null;
    }

    /**
     * @return Response
     */
    public function handlerHttp()
    {
        // TODO: container
        $router = new Router(self::options('labels'));

        $request = self::request();

        $match = self::routes($router)->match($request->getMethod(), $request->getUri());

        $handler = new HandlerHttp($request, $match, self::$options['separator']);

        return $handler->apply();
    }

    /**
     * @return Request
     */
    public static function request()
    {
        if (!self::$REQUEST) {
            // TODO: container
            $request = new Request(self::$options['strict']);
            self::$REQUEST = $request->fromServer();
        }
        return self::$REQUEST;
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
        $filename = path(true, 'config', $name . '.php');
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
            $router->load(path(true, $file));
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
     * @param $service
     */
    public function cli($service)
    {
        switch ($service) {
            case 'route': {
                RouteService::execute($this);
                break;
            }

        }
    }

}