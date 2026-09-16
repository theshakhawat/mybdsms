<?php


namespace App\Services;

class NagadService
{
    public static function encryptPublicKey($data)
    {
        $publicKey = file_get_contents(config('services.nagad.public_key_path'));
        openssl_public_encrypt($data, $cryptText, $publicKey);
        return base64_encode($cryptText);
    }

    public static function decryptPrivateKey($data)
    {
        $privateKey = file_get_contents(config('services.nagad.private_key_path'));
        openssl_private_decrypt(base64_decode($data), $decrypted, $privateKey);
        return $decrypted;
    }

    public static function generateSignature($data)
    {
        $privateKey = file_get_contents(config('services.nagad.private_key_path'));
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public static function getClientIp()
    {
        return request()->ip() === '127.0.0.1' ? '102.13.12.1' : request()->ip();
    }
}
