<?php

namespace Simples\Core\Gateway;

/**
 * Class Request
 * @package Simples\Core\Gateway
 */
class Request
{
    /**
     * @var string
     */
    private $uri = '';
    /**
     * @var string
     */
    private $method = 'GET';
    /**
     * @var string
     */
    private $url = '';

    /**
     * @var array
     */
    private $data = [];
    /**
     * @var array
     */
    private $input = [];

    /**
     * Request constructor.
     */
    public function __construct()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->http();
        } else {
            $this->cli();
        }
    }

    /**
     *
     */
    private function http()
    {
        $server = $_SERVER;

        $self = isset($server['PHP_SELF']) ? $server['PHP_SELF'] : '';
        $request_uri = isset($server['REQUEST_URI']) ? explode('?', $server['REQUEST_URI'])[0] : '';

        $peaces = explode('/', $self);
        array_pop($peaces);

        $start = implode('/', $peaces);
        $search = '/' . preg_quote($start, '/') . '/';
        $uri = preg_replace($search, '', $request_uri, 1);

        $this->uri = $uri;
        $this->method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : $this->method;
        $this->url = isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] . $start : $this->url;

        $this->uri = isset($_GET['_uri']) ? $_GET['_uri'] : $this->uri;
        $this->uri = isset($_POST['_uri']) ? $_POST['_uri'] : $this->uri;

        $this->method = isset($_GET['_method']) ? $_GET['_method'] : $this->method;
        $this->method = isset($_POST['_method']) ? $_POST['_method'] : $this->method;

        $this->url = isset($_GET['_url']) ? $_GET['_url'] : $this->url;
        $this->url = isset($_POST['_url']) ? $_POST['_url'] : $this->url;


        $this->uri = substr($this->uri, -1) !== '/' ? $this->uri . '/' : $this->uri ;
        $this->method = strtoupper($this->method);

        $this->set('GET', $_GET);
        $this->set('POST', $_POST);
        $this->set($this->method, json_decode(file_get_contents("php://input")));
    }

    /**
     *
     */
    private function cli()
    {
        $argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : [];
        array_shift($argv);

        $cli = [];
        foreach ($argv as $arg) {
            $in = explode("=", $arg);
            if (count($in) == 2) {
                $cli[$in[0]] = $in[1];
            } else {
                $cli[$in[0]] = 0;
            }
        }

        if (isset($cli['-uri'])) {
            $this->uri = $cli['-uri'];
        }
        if (isset($cli['-method'])) {
            $this->method = $cli['-method'];
        }
        if (isset($cli['-url'])) {
            $this->url = $cli['-url'];
        }


        $this->uri = substr($this->uri, -1) !== '/' ? $this->uri . '/' : $this->uri ;
        $this->method = strtoupper('cli');

        $this->set('CLI', $cli);
    }

    /**
     * @param $source
     * @param $data
     */
    private function set($source, $data)
    {
        $this->data[$source] = $data;

        if (isset($this->data[$source]['_method'])) {
            unset($this->data[$source]['_method']);
        }

        if (is_array($this->data[$source]) or is_object($this->data[$source])) {

            foreach ($this->data[$source] as $key => $value) {
                $this->input[$key] = [
                    'value' => $value, 'source' => $source
                ];
            }
        }
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->input;
    }

}