<?php

namespace Simples\Core\Gateway;


class Header
{
    /**
     * @var string
     */
    public $string;

    /**
     * @var bool
     */
    public $replace;

    /**
     * @var int
     */
    public $status;

    /**
     * Header constructor.
     * @param string $string
     * @param bool $replace
     * @param int $status
     */
    public function __construct($string, $replace = true, $status = 200)
    {
        $this->string = $string;
        $this->replace = $replace;
        $this->status = $status;
    }


}