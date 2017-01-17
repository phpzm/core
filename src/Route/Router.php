<?php

namespace Simples\Core\Route;

use Simples\Core\Kernel\App;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use Stringy\StaticStringy as stringy;

/**
 * Class Router
 * @package Simples\Core\Route
 *
 * @method Router get($route, $callable, $options = [])
 * @method Router post($route, $callable, $options = [])
 * @method Router put($route, $callable, $options = [])
 * @method Router patch($route, $callable, $options = [])
 * @method Router delete($route, $callable, $options = [])
 */
class Router extends Engine
{
    /**
     * TODO: exceptions to error input parameters
     * @param $method
     * @param $start
     * @param $context
     * @param array $options
     * @return $this
     */
    public function group($method, $start, $context, $options = [])
    {
        $type = '';

        switch (gettype($context)) {
            case TYPE_ARRAY: {
                foreach ($context as $index => $file) {
                    if (!file_exists(path(true, $file))) {
                        unset($context[$index]);
                    }
                }
                $type = 'files';
                break;
            }
            case TYPE_STRING: {
                if (file_exists(path(true, $context))) {
                    $type = 'file';
                    if (is_dir(path(true, $context))) {
                        $type = 'dir';
                    }
                }
                break;
            }
            case TYPE_OBJECT: {
                if (is_callable($context)) {
                    $type = 'callable';
                }
                break;
            }
        }
        $start = (substr($start, 0, 1) === '/' ?  $start : '/' . $start);
        $start = (substr($start, -1) === '/' ?  substr($start, 0, -1) : $start);

        $options['group'] = ['start' => $this->pattern($start)['pattern'] . '/', 'type' => $type];

        $uri = $start . '*';

        $this->on($method, $uri, $context, $options);

        return $this;
    }

    /**
     * @param $method
     * @param $callback
     * @param array $options
     * @return $this
     */
    public function otherWise($method, $callback, $options = [])
    {
        if ($method === '*') {
            $method = self::ALL;
        }
        if (!is_array($method)) {
            $method = [$method];
        }
        foreach ($method as $item) {
            $this->otherWise[strtolower($item)] = ['callback' => $callback, 'options' => $options];
        }

        return $this;
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
     * @param string $method
     * @param string $path
     * @param string $namespace
     * @param array $options
     * @return $this
     */
    public function mirror($method, $path, $namespace, $options = [])
    {
        $path = substr($path, -1) === '/' ? $path . '(.*)' : $path . '/(.*)';

        $this->on($method, $path, function ($path) use ($namespace, $options) {
            $fragments = explode('/', $path);

            $method = stringy::camelize(array_pop($fragments));

            $peaces = array_map(function ($peace) {
                return stringy::upperCamelize($peace);
            }, $fragments);

            $class = implode('\\', $peaces);

            $use = (($namespace[0] !== '\\') ? ('\\' . $namespace) : ($namespace)) . '\\' . $class;

            //return $this->resolve("{$use}@{$method}", [$this->data], $options);
        });

        return $this;
    }
}
