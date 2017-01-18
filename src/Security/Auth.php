<?php

namespace Simples\Core\Security;

use Simples\Core\Helper\Text;
use Simples\Core\Kernel\App;

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
     * @param $hash
     * @return bool
     */
    public static function match($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * @return string
     */
    public static function getToken()
    {
        return App::request()->getHeader(env('AUTH_TOKEN'));
    }

    /**
     * @param $embed
     * @return string
     */
    public static function createToken($embed)
    {
        return guid() . '-' . Text::pad($embed, 10, 'F');
    }

    /**
     * @return array
     */
    public static function getEmbedValue()
    {
        $peaces = explode('-', self::getToken());
        return Text::replace($peaces[count($peaces) - 1], 'F', '');
    }
}
