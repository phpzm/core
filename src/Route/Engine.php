<?php

namespace Simples\Core\Route;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Simples\Core\Http\Response;
use Simples\Core\Kernel\App;
use Simples\Core\Kernel\Container;

/**
 * Class Engine
 * @package Simples\Core\Route
 */
class Engine
{
    /**
     * @trait Share
     */
    use Sharable;

    /**
     * @var array honorable mention ['options']
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
     * @var bool
     */
    protected $labels;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $headers;

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * Engine constructor.
     * @param bool $labels
     * @param string|null $contentType
     * @param array|null $headers
     */
    public function __construct(bool $labels = false, string $contentType = null, array $headers = null)
    {
        $this->labels = $labels;
        $this->type = of($contentType, Response::CONTENT_TYPE_PLAIN);
        $this->headers = $headers;
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this
     */
    final public function __call($method, $arguments)
    {
        if (!isset($arguments[1])) {
            return $this;
        }
        $uris = $arguments[0];
        if (!is_array($uris)) {
            $uris = [$uris];
        }
        $callback = $arguments[1];
        $options = isset($arguments[2]) ? $arguments[2] : [];

        foreach ($uris as $uri) {
            $this->on($method, $uri, $callback, $options);
        }
        return $this;
    }

    /**
     * @return $this
     */
    final public function clear()
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
    final public function on($methods, $uri, $callback, $options = [])
    {
        if ($methods === '*') {
            $methods = self::ALL;
        }
        if (gettype($methods) === 'string') {
            $methods = explode(',', $methods);
        }

        foreach ($methods as $method) {
            $method = strtolower($method);
            if (!isset($this->routes[$method])) {
                $this->routes[$method] = [];
            }
            $pattern = $this->pattern($uri);

            $route = $pattern['pattern'] . '$/';

            $this->routes[$method][$route] = [
                'uri' => $uri, 'callback' => $callback, 'options' => $options, 'labels' => $pattern['labels']
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
    final public function match($method, $uri, $options = [])
    {
        $method = strtolower($method);

        $path = null;
        $callback = null;
        $data = [];

        foreach ($this->routes as $index => $routes) {
            foreach ($routes as $path => $context) {

                // TODO: simplify this
                if (preg_match($path, $uri, $parameters)) {
                    $options = array_merge($context['options'], $options);

                    if ($method === $index || (off($options, 'cors') && $method === $this->preFlight)) {
                        array_shift($parameters);

                        $callback = $context['callback'];
                        $labels = $context['labels'];
                        $data = $parameters;
                        if ($this->labels || (isset($options['labels']) ? $options['labels'] : false)) {
                            foreach ($labels as $key => $label) {
                                $data[$label] = $parameters[$key];
                            }
                        }
                        break;
                    }
                }
            }
        }
        $parameters = array_merge($data, ['data' => $this->data]);

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
        // TODO: simplify
        $group = off($options, 'group');

        if ($group) {
            unset($options['group']);

            $this->clear();

            $this->deep($group['type'], $callback);

            $end = str_replace_first($group['start'], '', $uri);
            $uri = (substr($end, 0, 1) === '/') ? $end : '/' . $end;

            return $this->match($method, $uri, $options);
        }

        if (isset($options['type']) || $this->type) {
            App::options('type', isset($options['type']) ? $options['type'] : $this->type);
        }
        if (isset($options['headers']) || $this->headers) {
            $headers = $this->headers;
            if (!$headers) {
                $headers = [];
            }
            if (isset($options['headers'])) {
                $headers = array_merge($headers, $options['headers']);
            }
            App::options('headers', $headers);
        }

        return new Match($method, $uri, $path, $callback, $parameters, $options);
    }

    /**
     * @param $type
     * @param $callback
     */
    protected function deep($type, $callback)
    {
        switch ($type) {
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
                call_user_func_array($callback, Container::box()->resolveFunctionParameters($callback, [$this]));
                break;
            }
        }
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
            }
            $pattern = '/' . preg_quote(App::options('root'), '/') . '/';
            $file = preg_replace($pattern, '', $resource->getPathname(), 1);
            if ($file) {
                $files[] = $file;
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
            } elseif (strpos($value, '{') === 0) {
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

    /**
     * @param array $trace
     * @return array
     */
    public function getTrace($trace = [])
    {
        $groups = [];
        foreach ($this->routes as $method => $paths) {
            foreach ($paths as $route) {
                $trace[] = [
                    'method' => $method,
                    'uri' => $route['uri'],
                    'options' => $route['options'],
                    'callback' => stripslashes(json_encode($route['callback']))
                ];
                $group = off($route['options'], 'group');
                if ($group) {
                    $groups[] = [
                        'type' => $group['type'], 'callback' => $route['callback']
                    ];
                }
            }
        }

        foreach ($this->otherWise as $method => $othersWise) {
            $trace[] = [
                'method' => $method,
                'uri' => '/other-wise',
                'options' => $othersWise['options'],
                'callback' => stripslashes(json_encode($othersWise['callback']))
            ];
        }
        $this->otherWise = [];

        if (count($groups)) {
            foreach ($groups as $group) {
                $this->clear();
                $this->deep($group['type'], $group['callback']);

                $trace = $this->getTrace($trace);
            }
        }

        return $trace;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addHeader($name, $value)
    {
        if (!is_array($this->headers)) {
            $this->headers = [];
        }
        $this->headers[$name] = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}
