<?php

namespace Simples\Core\Model;

/**
 * Class Relation
 * @package Simples\Core\Model
 */
class Relation
{
    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $source;

    /**
     * Relation constructor.
     * @param string $target
     * @param string $class
     * @param string $source
     */
    public function __construct($target, $class, $source)
    {
        $this->target = $target;
        $this->class = $class;
        $this->source = $source;
    }
}
