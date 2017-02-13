<?php

namespace Simples\Core\Security;

use Simples\Core\Error\RunTimeError;
use Simples\Core\Helper\Json;

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
        $peaces = explode('.', $token);
        if (count($peaces) !== 3) {
            throw new RunTimeError("The token '{$token}' is invalid");
        }
        return (array)Json::decode(Encryption::decode(base64_decode($peaces[1]), $secret));
    }
}
