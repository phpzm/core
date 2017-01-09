<?php

namespace Simples\Core\Security;


/**
 * Class Auth
 * @package Simples\Core\Security
 */
class Auth
{
    /**
     * @param $password
     * @return string
     */
    public static function crypt($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    /**
     * @param $password
     * @param $candidate
     * @return bool
     */
    public static function match($password, $candidate)
    {
        return password_verify($password, $candidate);
    }

}