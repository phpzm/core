<?php

namespace Simples\Core\Persistence;

/**
 * Class Fusion
 * @package Simples\Core\Persistence
 */
class Fusion
{
    /**
     * @var string
     */
    private $referenced;

    /**
     * @var string
     */
    private $collection;

    /**
     * @var string
     */
    private $references;

    /**
     * @var bool
     */
    private $exclusive;

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * Fusion constructor.
     * @param string $referenced
     * @param string $collection
     * @param string $references
     * @param bool $exclusive
     */
    public function __construct($referenced, $collection, $references, $exclusive = false)
    {
        $this->referenced = $referenced;
        $this->collection = $collection;
        $this->references = $references;
        $this->exclusive = $exclusive;
    }

    /**
     * @return string
     */
    public function getReferenced(): string
    {
        return $this->referenced;
    }

    /**
     * @param string $referenced
     * @return Fusion
     */
    public function setReferenced(string $referenced): Fusion
    {
        $this->referenced = $referenced;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     * @return Fusion
     */
    public function setCollection(string $collection): Fusion
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferences(): string
    {
        return $this->references;
    }

    /**
     * @param string $references
     * @return Fusion
     */
    public function setReferences(string $references): Fusion
    {
        $this->references = $references;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    /**
     * @param bool $exclusive
     * @return Fusion
     */
    public function setExclusive(bool $exclusive): Fusion
    {
        $this->exclusive = $exclusive;
        return $this;
    }
}
