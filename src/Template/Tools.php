<?php

namespace Simples\Core\Template;

use Simples\Core\Kernel\App;

/**
 * Class Tools
 * @package Simples\Core\Template
 */
class Tools
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param string $path
     * @param bool $print
     * @return string
     */
    protected function href(string $path, $print = true)
    {
        $route = App::route($path);
        if ($print) {
            out($route);
        }
        return $route;
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param bool $print
     * @return string
     */
    public function here($print = true)
    {
        return $this::href($this->uri(), $print);
    }

    /**
     * @return string
     */
    public function uri()
    {
        return substr(App::request()->getUri(), 0, -1);
    }

    /**
     * @param $href
     * @return bool
     */
    public function match($href)
    {
        return strpos(App::request()->getUri(), $href) === 0;
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param string $path
     * @param bool $print
     * @return string
     */
    public function image($path, $print = true)
    {
        return $this->asset('images/' . $this->fix($path), $print);
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param string $path
     * @param bool $print
     * @return string
     */
    public function style($path, $print = true)
    {
        return $this->asset('styles/' . $this->fix($path), $print);
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param string $path
     * @param bool $print
     * @return string
     */
    public function script($path, $print = true)
    {
        return $this->asset('scripts/' . $this->fix($path), $print);
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param $path
     * @param bool $print
     * @return string
     */
    public function asset($path, $print = true)
    {
        return $this->href('assets/' . $this->fix($path) . $this->test(), $print);
    }

    /**
     * @param string $path
     * @return string
     */
    private function fix($path)
    {
        return (gettype($path) === TYPE_STRING && $path{0} === '/') ? substr($path, 1) : $path;
    }

    /**
     * @param $value
     * @param $index
     * @return string
     */
    public function stamp($value, $index = null)
    {
        if (is_null($index)) {
            return out($value);
        }
        return $this->off($value, $index);
    }

    /**
     * @param $value
     * @param $index
     * @return string
     */
    public function off($value, $index)
    {
        return out(off($value, $index));
    }

    /**
     * @param $index
     * @return string
     */
    public function out($index)
    {
        return out($this->get($index));
    }

    /**
     * @param $index
     * @param $default
     * @return mixed
     */
    protected function get($index = null, $default = null)
    {
        if (is_null($index)) {
            return $this->data;
        }
        return off($this->data, $index, $default);
    }

    /**
     * @return string
     */
    private function test()
    {
        return env('TEST_MODE') ? '?c=' . uniqid() : '';
    }
}
