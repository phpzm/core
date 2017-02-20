<?php

namespace Simples\Core\Kernel;

use ErrorException;
use Simples\Core\Console\ControllerService;
use Simples\Core\Console\HelpService;
use Simples\Core\Console\ModelService;
use Simples\Core\Console\RepositoryService;
use Simples\Core\Console\RouteService;
use Simples\Core\Console\Service;
use Simples\Core\Http\Request;
use Simples\Core\Http\Response;
use Simples\Core\Persistence\Transaction;
use Throwable;

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
     * @var array
     */
    private static $OPTIONS;

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
        static::start($options);
    }

    /**
     * @param array $options
     * @return array
     */
    private static function start(array $options = [])
    {
        if (!self::$OPTIONS) {
            $default = [
                'root' => dirname(__DIR__, 5),
                'lang' => [
                    'default' => 'en', 'fallback' => 'en'
                ],
                'labels' => true,
                'headers' => [],
                'type' => Response::CONTENT_TYPE_HTML,
                'separator' => '@',
                'filter' => '~>',
                'strict' => false
            ];
            self::$OPTIONS = array_merge($default, $options);
        }
        return self::$OPTIONS;
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
        self::start();
        if ($key) {
            if (!$value) {
                return self::$OPTIONS[$key] ?? null;
            }
            self::$OPTIONS[$key] = $value;
        }
        return self::$OPTIONS;
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
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
        } catch (Throwable $throw) {
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
                case 'controller':
                    ControllerService::execute($this);
                    $service = '';
                    break;
                case 'repository':
                    RepositoryService::execute($this);
                    $service = '';
                    break;
            };
            if (!$service || $service === 'help') {
                HelpService::execute($this);
            }

            $service = read();
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
            $request = new Request(self::$OPTIONS['strict']);
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

        $config = null;
        if (isset(self::$CONFIGS[$name])) {
            $config = self::$CONFIGS[$name];
        }
        if (!$config) {
            $filename = path(true, "config/{$name}.php");
            if (file_exists($filename)) {
                /** @noinspection PhpIncludeInspection */
                $config = (object) require $filename;
                self::$CONFIGS[$name] = $config;
            }
        }
        if (count($peaces) === 0) {
            return $config;
        }

        return search((array)$config, $peaces);
    }

    /**
     * Get value default created in defaults config to some class
     * @param string $class
     * @param string $property
     * @return mixed
     */
    public static function defaults(string $class, string $property)
    {
        return static::config("defaults.{$class}.{$property}");
    }

    /**
     * Simple helper to generate a valid route to resources of project
     *
     * Ex.: `self::route('/download/images/picture.png')`, will print //localhost/download/images/picture.png
     *
     * @param string $uri Path to route
     * @return string
     */
    public static function route($uri)
    {
        return '//' . self::request()->getUrl() . '/' . ($uri{0} === '/' ? substr($uri, 1) : $uri);
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param array $trace
     * @param bool $filter
     * @return array
     */
    public static function beautifulTrace(array $trace, bool $filter = true): array
    {
        $stack = [];
        stop($trace);
        foreach ($trace as $value) {
            $trace = off($value, 'function');
            if ($trace === 'call_user_func_array') {
                continue;
            }
            $class = off($value, 'class');
            $function = off($value, 'function');
            if ($filter && strpos($class, 'Simples\\Core\\Kernel') === 0) {
                continue;
            }
            if ($class && $function) {
                $trace = $class . App::options('separator') . $function;
            }
            $stack[] = $trace;
        }
        return $stack;
    }
}
