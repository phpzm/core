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

        $self = isset($server['PHP_SELF']) ? str_replace('index.php/', '', $server['PHP_SELF']) : '';
        $uri = isset($server['REQUEST_URI']) ? explode('?', $server['REQUEST_URI'])[0] : '';
        $start = '';

        if ($self !== $uri) {

            $peaces = explode('/', $self);
            array_pop($peaces);

            $start = implode('/', $peaces);
            $search = '/' . preg_quote($start, '/') . '/';
            $uri = preg_replace($search, '', $uri, 1);
        }

        $this->uri = $uri;
        $this->method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : $this->method;
        $this->url = isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] . $start : $this->url;

        foreach (['uri', 'method', 'url'] as $item) {
            $this->$item = isset($_GET["_{$item}"]) ? $_GET["_{$item}"] : $this->$item;
            $this->$item = isset($_POST["_{$item}"]) ? $_POST["_{$item}"] : $this->$item;
        }

        $this->uri = substr($this->uri, -1) !== '/' ? $this->uri . '/' : $this->uri ;
        $this->method = strtoupper($this->method);

        $_PAYLOAD = (array) json_decode(file_get_contents("php://input"));
        if ($_PAYLOAD) {
            $_PAYLOAD = [];
        }
        $this->set('GET', $_GET);
        $this->set($this->method === 'GET' ? 'POST' : $this->method, array_merge($_POST, $_PAYLOAD));

        $_GET = [];
        $_POST = [];
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