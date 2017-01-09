<?php

namespace Simples\Core\Flow;

/**
 * Class Engine
 * @package Simples\Core\Flow
 */
class Engine
{
    /**
     * @trait Share
     */
    use Sharable;

    /**
     * @var array
     */
    const ALL = ['get', 'post', 'put', 'patch', 'delete'];

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var array
     */
    public $debug = [];

    /**
     * @var array
     */
    protected $otherWise = [];

    /**
     * @var string
     */
    private $preFlight = 'options';

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public final function __call($name, $arguments)
    {
        if (!isset($arguments[1])) {
            return $this;
        }
        return $this->on($name, $arguments[0], $arguments[1], isset($arguments[2]) ? $arguments[2] : []);
    }

    /**
     * @return $this
     */
    public final function clear()
    {
        $this->routes = [];

        return $this;
    }

    /**
     * @param $methods
     * @param $uri
     * @param $callback
     * @param array $options
     * @return $this
     */
    public final function on($methods, $uri, $callback, $options = [])
    {
        if (gettype($methods) === 'string') {
            if ($methods === '*') {
                $methods = self::ALL;
            } else {
                $methods = explode(',', $methods);
            }
        }

        foreach ($methods as $method) {

            $method = strtolower($method);
            if (!isset($this->routes[$method])) {
                $this->routes[$method] = [];
            }
            $pattern = $this->pattern($uri);

            $route = $pattern['pattern']  . '$/';

            $this->routes[$method][$route] = [
                'callback' => $callback, 'options' => $options, 'labels' => $pattern['labels']
            ];
        }

        return $this;
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return null
     */
    public final function match($method, $uri, $options = [])
    {
        $method = strtolower($method);

        $path = '';
        $callback = null;
        $parameters = [$this->data];

        foreach ($this->routes as $index => $routes) {

            foreach ($routes as $path => $context) {

                // TODO: simplify this
                if (preg_match($path, $uri, $params)) {

                    $options = array_merge($context['options'], $options);

                    if ($method === $index || (off($options, 'cors') && $method === $this->preFlight)) {

                        array_shift($params);

                        $parameters = array_merge($params, $parameters);
                        $callback = $context['callback'];

                        break;
                    }
                }
            }
        }

        if (!$callback && isset($this->otherWise[$method])) {

            $context = $this->otherWise[$method];

            $path = '';
            $callback = $context['callback'];
            $options = array_merge($context['options'], $options);
        }

        return $this->resolve($method, $uri, $path, $callback, $parameters, $options);
    }

    /**
     * @param $method
     * @param $uri
     * @param $path
     * @param $callback
     * @param $parameters
     * @param $options
     * @return Match
     */
    protected function resolve($method, $uri, $path, $callback, $parameters, $options)
    {
        $group = off($options, 'group');

        if ($group) {

            unset($options['group']);

            $this->clear();

            switch ($group['type']) {
                case 'file': {
                    $this->load(path(true, $callback));
                    break;
                }
                case 'files': {
                    foreach ($callback as $file) {
                        $this->load(path(true, $file));
                    }
                    break;
                }
                case 'dir': {
                    $files = $this->files($callback);
                    foreach ($files as $file) {
                        $this->load(path(true, $file));
                    }
                    break;
                }
                case 'callable': {
                    call_user_func_array($callback, fill_parameters($callback, [$this]));
                    break;
                }
            }

            $end = str_replace_first($group['start'], '', $uri);
            $uri = (substr($end, 0, 1) === '/') ? $end : '/' . $end;

            return $this->match($method, $uri, $options);
        }

        return new Match($method, $uri, $path, $callback, $parameters, $options);
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
    public function files($dir)
    {
        $files = [];

        $dir = path(true, $dir);

        if (!is_dir($dir)) {
            return $files;
        }

        $resources = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::SELF_FIRST);
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

    /**
     * @param $uri
     * @return array
     */
    public function pattern($uri)
    {
        $labels = [];
        $uri = (substr($uri, 0, 1) !== '/') ? '/' . $uri : $uri;
        $peaces = explode('/', $uri);
        foreach ($peaces as $key => $value) {
            $peaces[$key] = str_replace('*', '(.*)', $peaces[$key]);
            if (strpos($value, ':') === 0) {
                $peaces[$key] = '(\w+)';
                $labels[] = substr($value, 1);
            } else if (strpos($value, '{') === 0) {
                $peaces[$key] = '(\w+)';
                $labels[] = substr($value, 1, -1);
            }
        }
        if ($peaces[(count($peaces) - 1)]) {
            $peaces[] = '';
        }
        $pattern = str_replace('/', '\/', implode('/', $peaces));
        return [
            'pattern' => '/^' . $pattern,
            'labels' => $labels
        ];
    }

}
