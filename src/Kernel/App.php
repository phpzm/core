<?php

namespace Simples\Core\Kernel;

use Simples\Core\Flow\Router;
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
     * @var Response
     */
    private static $RESPONSE = null;

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
     * @param $type
     * @return mixed
     */
    public function handler($type)
    {
        $router = new Router();
        switch ($type)
        {
            case 'http': {
                $request = self::request()->fromServer();
                $method = $request->getMethod();
                $uri = $request->getUri();
                return (new Handler(self::request(), self::response()))(self::routes($router)->match($method, $uri));
                break;
            }
        }
        return null;
    }

    /**
     *
     */
    public function http()
    {
        $response = $this->handler('http');

        if (!($response instanceof Response)) {
            $response = self::response()->plain($response);
        }

        $headers = $response->getHeaders();

        foreach ($headers as $name => $value) {
            header(implode(':', [$name, $value]), true);
        }

        http_response_code($response->getStatusCode());
        //header(implode(':', ['Status-Reason-Phrase', $response->getReasonPhrase()]), true);

        $response = $response->getBody()->getContents();

        if ($response) {
            out($response);
        }
    }

    /**
     * @return Request
     */
    public static function request()
    {
        if (!self::$REQUEST) {
            self::$REQUEST = new Request(self::$options['strict']);
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
        $filename = path(true, 'config', $name . '.php');
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            $config = require $filename;
        }
        self::$CONFIGS[$name] = (object)$config;

        return self::$CONFIGS[$name];
    }

    /**
     * @param $property
     * @return mixed
     */
    public static function env($property)
    {
        return off(self::config(env()), $property);
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
     * @param $output
     */
    public static function cli($output)
    {

    }

}