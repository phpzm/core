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
     * @var array
     */
    private $details;

    /**
     * RunTimeError constructor.
     * @param string $message
     * @param array $details
     */
    public function __construct($message = '', array $details = [])
    {
        parent::__construct($message, 0, null);

        $this->details = $this->parse($details);
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
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
