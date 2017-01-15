<?php

namespace Simples\Core\Kernel;

use Exception;
use Simples\Core\Http\Request;
use Simples\Core\Http\Response;
use Simples\Core\Route\Match;
use Simples\Core\Route\Wrapper;
use Throwable;

/**
 * Class HandlerHttp
 * @package Simples\Core\Kernel
 */
class HandlerHttp extends Response
{
    /**
     * @var Request
     */
    private $__request;

    /**
     * @var Match
     */
    private $__match;

    /**
     * @var Container
     */
    private $__container;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var string
     */
    private $headerOrigin = 'Origin';

    /**
     * @var string
     */
    private $headerAccessControlRequestHeaders = 'Access-Control-Request-Headers';

    /**
     * HandlerHttp constructor.
     * @param Request $request
     * @param Match $match
     * @param string $separator
     */
    public function __construct(Request $request, Match $match, $separator = '@')
    {
        parent::__construct();

        $this->__request = $request;
        $this->__match = $match;
        $this->__container = $container = Container::getInstance();
        $this->separator = $separator;
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->__request;
    }

    /**
     * @return Match
     */
    public function match()
    {
        return $this->__match;
    }

    /**
     * @return Container
     */
    public function container()
    {
        return $this->__container;
    }

    /**
     * @return bool
     */
    private function isCors()
    {
        return (boolean)off($this->match()->getOptions(), 'cors');
    }

    /**
     * @return bool
     */
    private function isPreFlight()
    {
        return strtolower($this->request()->getMethod()) === 'options';
    }

    /**
     * @param $content
     * @return bool
     */
    private function isResponse($content)
    {
        return $content instanceof Response;
    }

    /**
     * @return mixed
     */
    private function getContentType()
    {
        return off($this->match()->getOptions(), 'type');
    }

    /**
     * @return mixed
     */
    public function apply()
    {
        $response = $this->resolve();
        if ($this->isCors()) {
            $response->cors($this->request()->getHeader($this->headerOrigin));
        }

        return $response;
    }

    /**
     * @return Response
     */
    private final function resolve()
    {
        if ($this->isCors() && $this->isPreFlight()) {

            return
                $this
                    ->cors($this->request()->getHeader($this->headerOrigin))
                    ->preFlight($this->request()->getHeader($this->headerAccessControlRequestHeaders));
        }

        /** @var mixed $callback */
        $callback = $this->match()->getCallback();

        if (!$callback) {
            return $this->parse(null);
        }

        if (gettype($callback) !== TYPE_OBJECT) {
            return $this->controller($callback);
        }

        return $this->call($callback->bindTo($this), $this->parameters($callback));
    }

    /**
     * @param $callback
     * @return Response
     */
    private function controller($callback)
    {
        // TODO: simplify
        switch (gettype($callback)) {
            case TYPE_ARRAY: {
                if (isset($callback[0]) && isset($callback[1])) {
                    $class = $callback[0];
                    $method = $callback[1];
                } else {
                    foreach ($callback as $key => $value) {
                        $class = $key;
                        $method = $value;
                    }
                }
                break;
            }
            case TYPE_STRING: {
                $peaces = explode($this->separator, $callback);
                $class = $peaces[0];
                $method = substr($this->match()->getUri(), 1, -1);
                if (isset($peaces[1])) {
                    $method = $peaces[1];
                }
            }
        }

        if (isset($class) && isset($method) && method_exists($class, $method)) {

            /** @var \Simples\Core\Http\Controller $controller */
            $controller = $this->container()->make($class);
            if (is_callable($controller)) {
                $controller($this->request(), $this, $this->match());
            }
            return $this->call([$controller, $method], $this->parameters($controller, $method));
        }

        return $this->parse(null);
    }

    /**
     * @param $callable
     * @param $method
     * @return array
     */
    private function parameters($callable, $method = null)
    {
        $data = is_array($this->match()->getParameters()) ? $this->match()->getParameters() : [];
        $options = $this->match()->getOptions();

        $labels = isset($options['labels']) ? $options['labels'] : true;
        if ($method) {
            return $this->container()->resolveMethodParameters($callable, $method, $data, $labels);
        }
        return $this->container()->resolveFunctionParameters($callable, $data, $labels);
    }

    /**
     * @param $callback
     * @param array $parameters
     * @return Response
     */
    private function call($callback, $parameters)
    {
        ob_start();

        try {
            $result = call_user_func_array($callback, $parameters);
        }
        catch (Exception $error) {
            $result = $error;
        }

        $contents = ob_get_contents();
        if ($contents) {
            ob_end_clean();
            Wrapper::buffer($contents);
        }

        return $this->parse($result);
    }

    /**
     * @param $content
     * @return Response
     */
    private function parse($content) : Response
    {
        // TODO: organize usage of status codes
        $output = [];
        if (env('TEST_MODE')) {
            $output = Wrapper::messages();
        }

        if ($this->isResponse($content)) {
            /** @var Response $content */
            return $content->meta('output', $output);
        }

        $status = 200;
        if (empty($this->match()->getPath()) || is_null($content)) {
            $status = 404;
            if (is_null($content)) {
                $status = 501; // not implemented
            }
        }

        $meta = [
            'output' => $output
        ];
        if ($content instanceof Throwable) {
            $status = 500;
            if (env('TEST_MODE')) {
                $meta['trace'] = $content->getTrace();
            }
            $content = error_message($content);
        }

        $method = (string)$this->getContentType();

        return $this->$method($content, $status, $meta);
    }

}