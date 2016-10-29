<?php

namespace Simples\Core\Gateway;

use Simples\Core\App;
use Simples\Core\Template\Engine;

/**
 * Class Response
 * @package Simples\Core\Gateway
 */
class Response
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var mixed
     */
    private $body;

    /**
     * @param $string
     * @param bool $replace
     * @param int $code
     * @return $this
     */
    public function header($string, $replace = true, $code = 200)
    {
        $this->headers[] = new Header($string, $replace, $code);

        return $this;
    }

    /**
     * @param $view
     * @param null $data
     * @return $this
     */
    public function view($view, $data = null)
    {
        $engine = new Engine(path(true, App::config('app')->views['root']));

        $this->body = $engine->render($view, $data);

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function json($data)
    {
        $this->body = gettype($data) === TYPE_STRING ? $data : json_encode($data);

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return Response
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }


}