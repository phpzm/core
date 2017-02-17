<?php

namespace Simples\Core\Security;

use Simples\Core\Error\RunTimeError;
use Simples\Core\Helper\Json;
use Simples\Core\Http\Error\ForbiddenError;

/**
 * Class Jwt
 * @package Simples\Core\Security
 */
abstract class Jwt
{
    /**
     * @param array $data
     * @param string $secret
     * @return string
     */
    public static function create(array $data, string $secret): string
    {
        $header = base64_encode(json_encode(['type' => 'JWT', 'alg' => 'HS256']));

        $payload = base64_encode(Encryption::encode(Json::encode($data), $secret));

        $signature = base64_encode(hash_hmac('sha256', "{$header}.{$payload}", $secret, true));

        return "{$header}.{$payload}.{$signature}";
    }

    /**
     * @param string $token
     * @param string $secret
     * @return array
     * @throws RunTimeError
     */
    public static function payload(string $token, string $secret): array
    {
        if (!static::verify($token, $secret)) {
            throw new ForbiddenError("Token is not valid");
        }
        $peaces = explode('.', $token);
        if (count($peaces) !== 3) {
            throw new RunTimeError("The token '{$token}' is invalid");
        }
        return (array)Json::decode(Encryption::decode(base64_decode($peaces[1]), $secret));
    }

    /**
     * @param string $token
     * @param string $secret
     * @return bool
     */
    public static function verify(string $token, string $secret): bool
    {
        $peaces = explode('.', $token);
        if (count($peaces) > 3) {
            return false;
        }
        $header = $peaces[0];
        $payload = $peaces[1];
        $signature = $peaces[2];
        $hash = base64_encode(hash_hmac('sha256', "{$header}.{$payload}", $secret, true));
        if (function_exists('hash_equals')) {
            return hash_equals($signature, $hash);
        }
        $len = min(static::length($signature), static::length($hash));
        $status = 0;
        for ($i = 0; $i < $len; $i++) {
            $status |= (ord($signature[$i]) ^ ord($hash[$i]));
        }
        $status |= (static::length($signature) ^ static::length($hash));
        return ($status === 0);
    }

    /**
     * @param string $string
     * @return int
     */
    private static function length(string $string): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, '8bit');
        }
        return strlen($string);
    }
}
