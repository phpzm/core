<?php

namespace Simples\Core\Persistence\SQL\Error;

use Simples\Core\Persistence\Error\PersistenceError;

/**
 * Class PersistenceException
 * @package Simples\Core\Error
 */
class SQLDataError extends PersistenceError
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param array $details
     * @return array
     */
    protected function parse(array $details): array
    {
        foreach ($details as $detail) {
            switch (off($detail, 1)) {
                case 1452: {
                    $this->relationship(off($detail, 2));
                    break;
                }
                default:
                    return $details;
            }
        }
        return $this->errors;
    }

    /**
     * @param $message
     */
    private function relationship($message)
    {
        preg_match('/FOREIGN KEY \(`(\w+)`\)/', $message, $matches);
        array_shift($matches);
        if (count($matches) === 1) {
            $field = $matches[0];
            $this->errors[$field] = ['relationship'];
            return;
        }
        $this->errors[] = $message;
    }
}
