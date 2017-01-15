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
    private static $ENCRYPT_MODE = "AES-256-CBC";

    /**
     * @var string
     */
    private static $SECRET_IV = '0df1e73b-812f-9bcf-b07c-e8250e73e748';

    /**
     * @param $string
     * @param null $secretKey
     * @return string
     */
    public static function encode($string, $secretKey = null)
    {
        $key = hash('sha256', env('SECURITY', $secretKey));
        $iv = substr(hash('sha256', self::$SECRET_IV), 0, 16);

        return base64_encode(openssl_encrypt($string, self::$ENCRYPT_MODE, $key, 0, $iv));
    }

    /**
     * @param $string
     * @param null $secretKey
     * @return string
     */
    public static function decode($string, $secretKey = null)
    {
        $key = hash('sha256', env('SECURITY', $secretKey));
        $iv = substr(hash('sha256', self::$SECRET_IV), 0, 16);

        return openssl_decrypt(base64_decode($string), self::$ENCRYPT_MODE, $key, 0, $iv);
    }

}