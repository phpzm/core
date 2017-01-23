<?php

namespace Simples\Core\Kernel;

use Simples\Core\Http\Request;
use Simples\Core\Http\Response;
use Simples\Core\Route\Router;

/**
 * Class Http
 * @package Simples\Core\Kernel
 */
class Http
{
    /**
     * @var string
     */
    private $separator;

    /**
     * @var boolean
     */
    private $labels;

    /**
     * @var string
     */
    private $contentType;

    /**
     * Http constructor.
     * @param $separator
     * @param $labels
     * @param $contentType
     */
    public function __construct($separator, $labels, $contentType)
    {
        $this->separator = $separator;
        $this->labels = $labels;
        $this->contentType = $contentType;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handler(Request $request): Response
    {
        // TODO: container
        $router = new Router($this->separator, $this->labels, $this->contentType);

        // TODO: make routes here
        $match = App::routes($router)->match($request->getMethod(), $request->getUri());

        $handler = new HttpHandler($request, $match, $this->separator);

        return $handler->apply();
    }

    /**
     * @param Response $response
     */
    public function output(Response $response)
    {
        $headers = $response->getHeaders();
        foreach ($headers as $name => $value) {
            header(implode(':', [$name, $value]), true);
        }

        http_response_code($response->getStatusCode());

        $contents = $response->getBody()->getContents();
        if ($contents) {
            out($contents);
        }
    }
}
