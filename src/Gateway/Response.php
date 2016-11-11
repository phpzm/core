<?php

namespace Simples\Core\Gateway;

use Simples\Core\App;
use Simples\Core\Template\Engine;

/**
 * Class Response
 * @package Simples\Core\Gateway
 */
class Response extends ResponseStream
{
    /**
     * Response constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $this->write($engine->render($view, $data));

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function json($data)
    {
        $this->write(gettype($data) === TYPE_STRING ? $data : json_encode($data));

        return $this;
    }

}