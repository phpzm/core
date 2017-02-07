<?php

namespace Simples\Core\Security;

/**
 * Class Encryption
 * @package Simples\Core\Security
 */
class Encryption
{
    /**
     * @var string
     */
    const ENCRYPT_MODE = "AES-256-CBC";

    /**
     * @param string $string
     * @param string $secretKey
     * @return string
     */
    public static function encode($string, $secretKey): string
    {
        $key = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', md5($secretKey)), 0, 16);

        return base64_encode(openssl_encrypt($string, self::ENCRYPT_MODE, $key, 0, $iv));
    }

    /**
     * @param string $string
     * @param string $secretKey
     * @return string
     */
    public static function decode(string $string, string $secretKey): string
    {
        $key = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', md5($secretKey)), 0, 16);

        return openssl_decrypt(base64_decode($string), self::ENCRYPT_MODE, $key, 0, $iv);
    }
}
