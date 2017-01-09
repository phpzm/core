<?php

namespace Simples\Core\Kernel;

use Simples\Core\Flow\Match;
use Simples\Core\Http\Request;
use Simples\Core\Http\Response;

/**
 * Class Handler
 * @package Simples\Core\Kernel
 */
class Handler
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
     * @var Match
     */
    private $match;

    /**
     * @var string
     */
    private $separator = '::';

    /**
     * Handler constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param Match $match
     * @return mixed
     */
    public function __invoke(Match $match)
    {
        $this->match = $match;

        return $this->resolve();
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @return Match
     */
    public function match()
    {
        return $this->match;
    }

    /**
     * @return Response
     */
    private final function resolve()
    {
        if ($this->isCors() && $this->isPreFlight()) {

            return
                $this->response
                    ->cors($this->request->getHeader('Origin'))
                    ->preFlight($this->request->getHeader('Access-Control-Request-Headers'));
        }

        /** @var Callable $callback */
        $callback = $this->match->getCallback();

        if (!$callback) {
            return $this->parse('');
        }

        if (gettype($callback) === TYPE_STRING) {
            $peaces = explode($this->separator, $callback);
            if (!isset($peaces[1])) {
                return null;
            }
            return $this->controller($peaces[0], $peaces[1]);
        }

        return $this->call($callback->bindTo($this));
    }

    /**
     * @return bool
     */
    private function isCors()
    {
        $options = $this->match->getOptions();

        return isset($options['cors']) && $options['cors'];
    }

    /**
     * @return bool
     */
    private function isPreFlight()
    {
        return strtolower($this->request->getMethod()) === 'options';
    }

    /**
     * @param $response
     * @return Response
     */
    private function parse($response)
    {
        if (!$response instanceof Response) {
            $response = $this->response->plain($response);
        }
        if ($this->isCors()) {
            $response->cors($this->request->getHeader('Origin'));
        }
        return $response;
    }

    /**
     * @return Response
     */
    private function call($callback)
    {
        // TODO: use container DI if is defined in options
        $parameters = is_array($this->match->getParameters()) ? $this->match->getParameters() : [];

        return $this->parse(call_user_func_array($callback, fill_parameters($callback, $parameters)));
    }

    /**
     * @param $class
     * @param $method
     * @return Response
     */
    private function controller($class, $method)
    {
        if (method_exists($class, $method)) {

            /** @var \Simples\Core\Http\Controller $controller */
            $controller = new $class();
            if (is_callable($controller)) {
                $controller($this->request, $this->response, $this->match);
            }
            return $this->call([$controller, $method]);
        }
        // TODO: do something when not found method
        return $this->parse("Method '{$class}{$this->separator}{$method}' not found!");
    }

}