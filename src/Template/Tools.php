<?php
/*
 -------------------------------------------------------------------
 | @project: trabalho-final
 | @package: Simples\Core\Template
 | @file: ${FILE_NAME}
 -------------------------------------------------------------------
 | @user: william 
 | @creation: 28/10/16 01:42
 | @copyright: fagoc.br / gennesis.io / arraysoftware.net
 | @license: MIT
 -------------------------------------------------------------------
 | @description:
 | PHP class
 |
 */

namespace Simples\Core\Template;

use Simples\Core\App;

class Tools
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param $path
     * @param bool $print
     * @return string
     */
    protected function href($path, $print = true)
    {
        return App::route($path, $print);
    }

    /**
     * @param bool $print
     * @return string
     */
    public function here($print = true)
    {
        return App::route($this->uri(), $print);
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
     * @param string $path
     * @param bool $print
     * @return string
     */
    public function image($path, $print = true)
    {
        return $this->asset('images/' . $this->fix($path), $print);
    }

    /**
     * @param string $path
     * @param bool $print
     * @return string
     */
    public function style($path, $print = true)
    {
        return $this->asset('styles/' . $this->fix($path), $print);
    }

    /**
     * @param string $path
     * @param bool $print
     * @return string
     */
    public function script($path, $print = true)
    {
        return $this->asset('scripts/' . $this->fix($path), $print);
    }

    /**
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
    protected function get($index, $default = null)
    {
        return off($this->data, $index, $default);
    }

    /**
     * @return string
     */
    private function test()
    {
        return App::env('test') ? '?c=' . uniqid() : '';
    }

}