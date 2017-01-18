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
     * @param Request $request
     * @return Response
     */
    public function handler(Request $request): Response
    {
        // TODO: container
        $router = new Router(App::options('separator'), App::options('labels'), App::options('content-type'));

        $match = App::routes($router)->match($request->getMethod(), $request->getUri());

        $handler = new HttpHandler($request, $match, App::options('separator'));

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
