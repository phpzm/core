<?php

namespace Simples\Core\Http;

use Simples\Core\Kernel\App;
use Simples\Core\View\Template;

/**
 * Class Response
 * @package Simples\Core\Http
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
     * @param $name
     * @param string $value
     * @return $this
     */
    public function header($name, $value = '')
    {
        $this->headers[headerify($name)] = $value;

        return $this;
    }

    /**
     * @param $view
     * @param $data
     * @return $this
     */
    public function html($view, $data = [])
    {
        $engine = new Template(path(true, App::config('app')->views['root']));

        $this->write($engine->render($view, $data));

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function json($data)
    {
        if ($data) {
            $json = json_encode($data, JSON_NUMERIC_CHECK);

            $this->write($json);
        }

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function plain($data)
    {
        $scalars = [TYPE_STRING, TYPE_DATE, TYPE_BOOLEAN, TYPE_FLOAT, TYPE_INTEGER];
        if (in_array(gettype($data), $scalars)) {
            $this->write((string)$data);
            return $this;
        }

        return $this->json($data);
    }

    /**
     * @param $origin
     * @return $this
     */
    public function cors($origin)
    {
        return
            $this
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
    }

    /**
     * @param $allowed
     * @return $this
     */
    public function preFlight($allowed)
    {
        return $this
            ->plain('')
            ->header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS')
            ->header('Access-Control-Allow-Headers', $allowed);
    }

}