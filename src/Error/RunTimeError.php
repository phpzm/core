<?php

namespace Simples\Core\Error;

use Exception;

/**
 * Class RunTimeError
 * @package Simples\Core\Error
 */
class RunTimeError extends Exception
{
    /**
     * @var int
     */
    protected $status = 500;

    /**
     * @var array
     */
    private $details;

    /**
     * @var array
     */
    private $context;

    /**
     * RunTimeError constructor.
     * @param string $message
     * @param array $details
     * @param array $context
     */
    public function __construct($message = '', array $details = [], array $context = [])
    {
        parent::__construct($message, 0, null);

        $this->details = $this->parse($details);
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $details
     * @return array
     */
    protected function parse(array $details): array
    {
        return $details;
    }
}
