<?php

namespace Simples\Core\Kernel;

use Simples\Core\Console\HelpService;
use Simples\Core\Console\ModelService;
use Simples\Core\Console\RouteService;
use Simples\Core\Console\Service;
use Simples\Core\Http\Request;
use Simples\Core\Http\Response;
use Simples\Core\Persistence\Transaction;
use Simples\Core\Route\Router;
use Error;
use ErrorException;
use Exception;

/**
 * Class App
 * @package Simples\Core\Kernel
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
     * App constructor
     *
     * Create a instance of App Handler
     *
     * @param array $options ([
     *      'root' => string,
     *      'lang' => array,
     *      'labels' => boolean,
     *      'headers' => array,
     *      'type' => string
     *      'separator' => string
     *      'strict' => boolean
     *  ])
     */
    public function __construct($options)
    {
        $default = [
            'root' => __DIR__,
            'lang' => [
                'default' => 'en', 'fallback' => 'en'
            ],
            'labels' => true,
            'headers' => [],
            'type' => Response::CONTENT_TYPE_HTML,
            'separator' => '@',
            'strict' => false
        ];
        self::$options = array_merge($default, $options);

        self::$ROOT = off(self::$options, 'root');
    }

    /**
     * Management to options of app
     *
     * @param string $key (null) The id of option to get or set
     * @param string $value (null) The value to set
     * @return mixed Returns the entire options if there is no $key & no $value, else return the respective $option
     */
    public static function options($key = null, $value = null)
    {
        if ($key) {
            if (!$value) {
                return self::$options[$key] ?? null;
            }
            self::$options[$key] = $value;
        }
        return self::$options;
    }

    /**
     * Used to catch http requests and handle response to their
     *
     * @param bool $output (true) Define if the method will generate one output with the response
     * @return Response The match response for requested resource
     * @throws ErrorException Generated when is not possible commit the changes
     */
    public function http($output = true)
    {
        $fail = null;
        $response = null;
        $http = new Http(self::request());
        try {

            $response = $http->handler();
            if ($response->isSuccess()) {
                if (!Transaction::commit()) {
                    throw new ErrorException("Transaction can't commit the changes");
                }
            }

        } catch (Error $throw) {
            $fail = $throw;
        } catch (ErrorException $throw) {
            $fail = $throw;
        } catch (Exception $throw) {
            $fail = $throw;
        }

        if ($fail) {
            $response = $http->fallback($fail);
        }

        if ($output) {
            $http->output($response);
        }

        return $response;
    }

    /**
     * Handler to cli services, provide a interface to access services
     *
     * @param string $service The requested service
     */
    public function cli($service)
    {
        echo "@start/\n";
        echo "Press ^C or type 'exit' at any time to quit.\n";

        do {
            switch ($service) {
                case 'route': {
                    RouteService::execute($this);
                    $service = '';
                    break;
                }
                case 'model': {
                    ModelService::execute($this);
                    $service = '';
                    break;
                }
            };
            if (!$service || $service === 'help') {
                HelpService::execute($this);
            }

            echo "$ ";
            $service = trim(fgets(STDIN));
        } while (!in_array($service, Service::KILLERS));
    }

    /**
     * Singleton to Request to keep only one instance for each request
     *
     * @return Request Request object populated by server data
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
     * Interface to get config values
     * 
     * @param string $path The path of config ex.: "app.name", equivalent to Name of App
     * @return mixed Instance of stdClass with the all properties or the value available in path
     */
    public static function config($path)
    {
        $peaces = explode('.', $path);
        $name = $peaces[0];
        array_shift($peaces);

        if (isset(self::$CONFIGS[$name])) {
            return self::$CONFIGS[$name];
        }

        $config = [];
        $filename = path(true, "config/{$name}.php");
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            $config = require $filename;
        }
        self::$CONFIGS[$path] = (object)$config;

        if (!count($peaces)) {
            return self::$CONFIGS[$path];
        }

        return search($config, $peaces);
    }

    /**
     * Load the routes of project
     *
     * @param Router $router The router what will be used
     * @param array $files (null) If not informe will be used "route.files"
     * @return Router Object with the routes loaded in
     */
    public static function routes(Router $router, array $files = null)
    {
        $files = $files ? $files : self::config('route.files');

        foreach ($files as $file) {
            $router->load(path(true, $file));
        }

        return $router;
    }

    /**
     * Simple helper to generate a valid route to resources of project
     *
     * Ex.: `self::route('/download/images/picture.png')`, will print //localhost/download/images/picture.png
     *
     * @param string $uri Path to route
     * @param bool $print Output or not the route generated
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
}
