<?php

namespace Simples\Core\Security;

use Simples\Core\Kernel\App;

/**
 * Class Auth
 * @package Simples\Core\Security
 */
abstract class Auth
{
    /**
     * @var string
     */
    const PAYLOAD_USER = 'user', PAYLOAD_DEVICE = 'device';

    /**
     * @param string $password
     * @return string
     */
    public static function crypt(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     * @param string $candidate
     * @return bool
     */
    public static function match(string $password, string $candidate): bool
    {
        return password_verify($password, $candidate);
    }

    /**
     * @return string
     */
    public static function getToken()
    {
        return App::request()->getHeader(env('AUTH_TOKEN'));
    }

    /**
     * @param string $user
     * @param string $device
     * @param array $options
     * @return string
     */
    public static function createToken(string $user, string $device, array $options = []): string
    {
        $data = [
            self::PAYLOAD_USER => $user,
            self::PAYLOAD_DEVICE => $device
        ];
        return Jwt::create(array_merge($options, $data), env('SECURITY'));
    }

    /**
     * @param string $property
     * @return string
     */
    public static function getTokenValue(string $property): string
    {
        $token = self::getToken();
        if (!$token) {
            return '';
        }
        return off(Jwt::payload($token, env('SECURITY')), $property);
    }

    /**
     * @return string
     */
    public static function getUser(): string
    {
        return self::getTokenValue(self::PAYLOAD_USER);
    }

    /**
     * @return string
     */
    public static function getDevice(): string
    {
        return self::getTokenValue(self::PAYLOAD_DEVICE);
    }
}
