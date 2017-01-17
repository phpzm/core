<?php

namespace Simples\Core\Http;

class Input
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Input constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function number()
    {
        if (preg_match('/\d+(?:\.\d+)+/', $this->value, $matches)) {
            return $matches[0];
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function text()
    {
        if (is_scalar($this->value)) {
            return filter_var($this->value, FILTER_SANITIZE_STRING);
        }
        if (gettype($this->value) === TYPE_ARRAY) {
            return filter_var_array($this->value, FILTER_SANITIZE_STRING);
        }
    }

    /**
     * @return mixed
     */
    function __toString()
    {
        return $this->text();
    }

}