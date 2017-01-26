<?php

namespace Simples\Core\Data;

/**
 * Class Validation
 * @package Simples\Core\Data
 */
class Validation
{
    /**
     * @var array
     */
    private $rules = [];

    /**
     * Validation constructor.
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @param $field
     * @param $value
     * @param array ...$arguments
     * @return $this
     */
    public function add($field, $value, ...$arguments)
    {
        $rules = [];
        foreach ($arguments as $argument) {
            if (gettype($argument) === TYPE_STRING) {
                $rules[$argument] = '';
            } elseif (gettype($argument) === TYPE_ARRAY) {
                foreach ($argument as $rule => $options) {
                    $rules[$rule] = $options;
                }
            }
        }
        $this->rules[$field] = ['rules' => $rules, 'value' => $value];

        return $this;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->rules;
    }
}
