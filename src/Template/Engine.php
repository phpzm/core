<?php

namespace Simples\Core\Template;

/**
 * Class Engine
 * @package Simples\Core\Template
 */
class Engine extends Tools
{
    /**
     * @var string
     */
    private $root;

    /**
     * @var object
     */
    private $layout;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private $sections;

    /**
     * Engine constructor.
     * @param $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * @param $template
     * @param $data
     * @return string
     */
    public function render($template, $data)
    {
        $content = $this->compile($template, $data);

        while ($this->layout) {

            $layout = $this->layout;

            $this->sections[$layout->section] = $content;

            $this->layout = null;

            $content = $this->compile($layout->template, array_merge($layout->data, $this->data));
        }

        return $content;
    }

    /**
     * @param $template
     * @param $data
     * @return string
     */
    private function compile($template, $data)
    {
        $filename = path($this->root, $template);

        $this->data = $data;

        ob_start();
        if (file_exists($filename)) {

            extract($data);

            /** @noinspection PhpIncludeInspection */
            $callable = include $filename;

            if (is_callable($callable)) {
                call_user_func_array($callable, is_array($data) ? array_values($data) : [$data]);
            }
        }
        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    /**
     * @param $layout
     * @param $section
     * @param array $data
     */
    protected function extend($layout, $section, array $data = [])
    {
        $layout = (object)['section' => $section, 'template' => $layout, 'data' => $data];

        $this->layout =  $layout;
    }

    /**
     * @param $index
     * @param bool $print
     * @return mixed
     */
    protected function get($index, $print = true)
    {
        $get = sif($this->data, $index);
        if ($print) {
            out($get);
        }
        return $get;
    }

    /**
     * @param $name
     * @param bool $print
     * @return string
     */
    protected function section($name, $print = true)
    {
        $section = sif($this->sections, $name);
        if ($print) {
            out($section);
        }
        return $section;
    }

    /**
     * @param $template
     * @return mixed
     */
    protected function append($template)
    {
        $filename = path($this->root, $template);

        /** @noinspection PhpIncludeInspection */
        return include $filename;
    }

}