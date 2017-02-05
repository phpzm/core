<?php

namespace Simples\Core\Persistence;

/**
 * Interface Driver
 * @package Simples\Core\Persistence
 */
interface Driver
{
    /**
     * @return bool
     */
    public function start();

    /**
     * @return bool
     */
    public function commit();

    /**
     * @return bool
     */
    public function rollback();

    /**
     * @param array $clausules
     * @param array $values
     * @return string
     */
    public function create(array $clausules, array $values);

    /**
     * @param array $clausules
     * @param array $values
     * @return array
     */
    public function read(array $clausules, array $values = []);

    /**
     * @param array $clausules
     * @param array $values
     * @param array $filters
     * @return int
     */
    public function update(array $clausules, array $values, array $filters);

    /**
     * @param array $clausules
     * @param array $values
     * @return int
     */
    public function destroy(array $clausules, array $values);
}
