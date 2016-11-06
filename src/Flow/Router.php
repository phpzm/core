<?php

namespace Simples\Core\Flow;

use Simples\Core\App;
use Simples\Core\Gateway\Request;
use Simples\Core\Gateway\Response;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Class Router
 * @package Simples\Core\Flow
 */
class Router extends Engine
{
    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
    }

    /**
     * @param $uri
     * @param $class
     * @param array $options
     * @return $this
     */
    public function resource($uri, $class, $options = [])
    {
        $resource = [
            ['method' => 'GET', 'uri' => 'index', 'callable' => 'index'],

            ['method' => 'GET', 'uri' => '', 'callable' => 'index'],
            ['method' => 'GET', 'uri' => 'create', 'callable' => 'create'],
            ['method' => 'GET', 'uri' => ':id', 'callable' => 'show'],
            ['method' => 'GET', 'uri' => ':id/edit', 'callable' => 'edit'],

            ['method' => 'POST', 'uri' => '', 'callable' => 'store'],
            ['method' => 'PUT,PATCH', 'uri' => ':id', 'callable' => 'update'],
            ['method' => 'DELETE', 'uri' => ':id', 'callable' => 'destroy'],
        ];
        foreach ($resource as $item) {
            $item = (object)$item;
            $this->on($item->method, $uri . '/' . $item->uri, $class . '@' . $item->callable, $options);
        }

        return $this;
    }

    /**
     * @param $method
     * @param $start
     * @param $files
     * @param array $options
     * @return $this
     */
    public function group($method, $start, $files, $options = [])
    {
        $router = $this;

        $callback = function ($parameter) use ($router, $files, $options) {

            /** @var Router $router */

            if (!is_array($files)) {
                if (is_dir(path(true, $files))) {
                    $files = $this->files($files);
                } else {
                    $files = [$files];
                }
            }

            $router
                ->setUri($parameter . '/')
                ->clear();

            return App::routes($router, $files)->run();
        };

        $this->on($method, $start . '*', $callback, $options);

        return $this;
    }

    /**
     * @param $method
     * @param $callback
     * @param array $options
     * @return Router
     */
    public function otherWise($method, $callback, $options = [])
    {
        return $this->on($method, '/(.*)', $callback, $options);
    }

    /**
     * @param $filename
     */
    public function load($filename)
    {
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            $callable = require_once $filename;
            if (is_callable($callable)) {
                call_user_func_array($callable, [$this]);
            }
        }
    }

    /**
     * @param $dir
     * @return array
     */
    function files($dir)
    {
        $files = [];

        $dir = path(true, $dir);

        if (!is_dir($dir)) {
            return $files;
        }

        $resources = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($resources as $resource) {
            if (is_dir($resource->getFilename())) {
                continue;
            } else {
                $pattern = '/' . preg_quote(App::$ROOT, '/') . '/';
                $file = preg_replace($pattern, '', $resource->getPathname(), 1);
                if ($file) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

}
