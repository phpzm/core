<?php

namespace Simples\Core\Flow;

use Simples\Core\Gateway\Request;
use Simples\Core\Gateway\Response;

/**
 * Class Engine
 * @package Simples\Core\Flow
 */
class Engine extends Share
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var object
     */
    private $route;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    public $debug = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->uri = $this->request->getUri();
        $this->method = $this->request->getMethod();
    }

    /**
     * @param $name
     * @param $arguments
     * @return Router
     */
    public final function __call($name, $arguments)
    {
        if (!isset($arguments[1])) {
            return $this;
        }
        return $this->on($name, $arguments[0], $arguments[1], isset($arguments[2]) ? $arguments[2] : []);
    }

    /**
     * @return Request
     */
    public final function request()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public final function response()
    {
        return $this->response;
    }

    /**
     * @return object
     */
    public final function route()
    {
        return $this->route;
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
     * @return string
     */
    public final function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return Router
     */
    public final function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param $method
     * @param $uri
     * @param $callback
     * @param array $options
     * @return $this
     */
    public final function on($methods, $uri, $callback, $options = [])
    {
        if (gettype($methods) === 'string') {
            if ($methods === '*') {
                $methods = ['get', 'post', 'put', 'patch', 'delete'];
            } else {
                $methods = explode(',', $methods);
            }
        }

        foreach ($methods as $method) {

            $method = strtoupper($method);
            if (!isset($this->routes[$method])) {
                $this->routes[$method] = [];
            }
            $peaces = explode('/', $uri);
            foreach ($peaces as $key => $value) {
                $peaces[$key] = str_replace('*', '(.*)', $peaces[$key]);
                if (strpos($value, ':') === 0) {
                    $peaces[$key] = '(\w+)';
                }
            }
            if ($peaces[(count($peaces) - 1)]) {
                $peaces[] = '';
            }
            $pattern = str_replace('/', '\/', implode('/', $peaces));
            $route = '/^' . $pattern . '$/';

            $this->routes[$method][$route] = ['callback' => $callback, 'options' => $options];
        }

        return $this;
    }

    /**
     * @param $callback
     * @param array $params
     * @param array $options
     * @return mixed
     */
    private final function resolve($callback, array $params, array $options)
    {
        if (!is_callable($callback)) {
            $peaces = explode('@', $callback);
            if (!isset($peaces[1])) {
                return null;
            }
            $class = $peaces[0];
            $method = $peaces[1];

            if (method_exists($class, $method)) {

                /** @var \Simples\Core\Flow\Controller $controller */
                $controller = new $class($this->request(), $this->response(), $this->route);

                $callback = [$controller, $method];
            }
        }
        $params[] = array_merge($this->data, $options);

        return call_user_func_array($callback, $params);
    }

    /**
     * @return mixed
     */
    public final function run()
    {
        $method = $this->method;
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $context) {

            $this->debug[] = [
                'fetch' => [$route, $this->uri]
            ];

            if (preg_match($route, $this->uri, $params)) {

                array_shift($params);
                $options = $context['options'];

                $callback = $context['callback'];
                $this->route = (object)['method' => $method, 'uri' => $this->uri, 'route' => $route, 'callback' => $callback];
                $this->debug[] = [
                    'match' => [$route, $this->uri]
                ];

                return $this->resolve($callback, array_merge(array_values($params)), $options);
            }
        }

        return null;
    }

}
