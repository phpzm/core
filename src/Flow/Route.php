<?php

namespace Simples\Core\Flow;

/**
 * Class Route
 * @package Simples\Core\Flow
 */
class Route
{
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var string
     */
    private $path;
    /**
     * @var callable
     */
    private $callback;

    /**
     * Route constructor.
     * @param string $method
     * @param string $uri
     * @param string $path
     * @param string|callable $callback
     */
    public function __construct($method, $uri, $path, $callback)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->path = $path;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Route
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return Route
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Route
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     * @return Route
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

}