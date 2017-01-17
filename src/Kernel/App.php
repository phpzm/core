<?php

namespace Simples\Core\Kernel;

use Simples\Core\Console\HelpService;
use Simples\Core\Console\ModelService;
use Simples\Core\Console\RouteService;
use Simples\Core\Console\Service;
use Simples\Core\Helper\Text;
use Simples\Core\Http\Request;
use Simples\Core\Http\Response;
use Simples\Core\Persistence\Transaction;
use Simples\Core\Route\Router;

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
     * @param bool $output
     * @return Response
     * @throws \ErrorException
     */
    public function http($output = true)
    {
        $fail = null;

        try {
            $http = new Http();

            $response = $http->handler(self::request());

            if ($response->isSuccess()) {
                if (!Transaction::commit()) {
                    throw new \ErrorException("Transaction can't commit the changes");
                }
            }

            if ($output) {
                $http->output($response);
            }

            return $response;
        } catch (\Error $throw) {
            $fail = $throw;
        } catch (\ErrorException $throw) {
            $fail = $throw;
        } catch (\Exception $throw) {
            $fail = $throw;
        }

        echo 'Kernel Panic', throw_format($fail);

        return null;
    }

    /**
     * @param $service
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
     * @param $path
     * @return object
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
}
