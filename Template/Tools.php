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
     * @param $string
     * @param bool $print
     * @return string
     */
    public function image($string, $print = true)
    {
        return $this->asset('images/' . $string, $print);
    }

    /**
     * @param $string
     * @param bool $print
     * @return string
     */
    public function style($string, $print = true)
    {
        return $this->asset('styles/' . $string, $print);
    }

    /**
     * @param $string
     * @param bool $print
     * @return string
     */
    public function javascript($string, $print = true)
    {
        return $this->asset('scripts/' . $string, $print);
    }

    /**
     * @param $path
     * @param bool $print
     * @return string
     */
    public function asset($path, $print = true)
    {
        return $this->href('assets/' . $path, $print);
    }

}