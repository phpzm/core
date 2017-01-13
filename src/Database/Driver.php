<?php

namespace Simples\Core\Database;

/**
 * Interface Driver
 * @package Simples\Core\Database
 */
interface Driver
{
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