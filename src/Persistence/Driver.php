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
     * @param $clausules
     * @param array $values
     * @return null|string
     */
    public function create($clausules, array $values);

    /**
     * @param $clausules
     * @param array $values
     * @return array|null
     */
    public function read($clausules, array $values = []);

    /**
     * @param $clausules
     * @param $values
     * @param $filters
     * @return int
     */
    public function update($clausules, $values, $filters);

    /**
     * @param $clausules
     * @param array $values
     * @return int|null
     */
    public function destroy($clausules, array $values);
}
