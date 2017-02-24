<?php

namespace Simples\Core\Kernel;

use Simples\Core\Http\Request;
use Simples\Core\Http\Response;
use Simples\Core\Route\Match;
use Simples\Core\Route\Router;
use Throwable;

/**
 * Class Http
 * @package Simples\Core\Kernel
 */
class Http
{
    /**
     * @var array
     */
    const METHODS = ['get', 'post', 'put', 'patch', 'delete', 'options', 'head', 'find', 'purge', 'deletehard'];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Match
     */
    private $match;

    /**
     * Http constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Load the routes of project
     *
     * @param Router $router The router what will be used
     * @param array $files (null) If not informe will be used "route.files"
     * @return Router Object with the routes loaded in
     */
    public static function routes(Router $router, array $files = null)
    {
        $files = $files ? $files : App::config('route.files');

        foreach ($files as $file) {
            $router->load(path(true, $file));
        }

        return $router;
    }

    /**
     * @return Response
     */
    public function handler(): Response
    {
        // TODO: container
        $router = new Router(App::options('labels'), App::options('type'));

        // TODO: make routes here
        /** @var Match $match */
        $this->match = static::routes($router)->match($this->request->getMethod(), $this->request->getUri());

        $handler = new HttpHandler($this->request, $this->match);

        return $handler->apply();
    }

    /**
     * @param Throwable $fail
     * @return Response
     */
    public function fallback(Throwable $fail): Response
    {
        if (!$this->match) {
            $method = '';
            $uri = '';
            $path = '';
            $callback = null;
            $parameters = [];
            $options = [];
            $this->match = new Match($method, $uri, $path, $callback, $parameters, $options);
        }
        $this->match->setCallback($fail);

        $handler = new HttpHandler($this->request, $this->match);

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
