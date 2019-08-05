<?php

namespace App\Helpers;

use App\Exceptions\JwtException;
use Carbon\Carbon;

/**
 * Class JWT
 *
 * @package App\Helpers
 */
class JWT
{
    /**
     * Generate JWT token
     *
     * @param array $payload
     *
     * @return string
     *
     * @throws JwtException
     */
    public static function generateToken(array $payload)
    {
        try {
            $header = self::base64EncodeUrlSafe(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));

            if (!isset($payload['expiration'])) {
                $payload['expiration'] = Carbon::now()->addDay()->format('Y-m-d H:i:s');
            }

            $payload = self::base64EncodeUrlSafe(json_encode($payload));

            $signature = self::base64EncodeUrlSafe(hash_hmac('sha256', $header . '.' . $payload, env('APP_KEY'), true));

            return $header . "." . $payload . "." . $signature;
        } catch (\Exception $e) {
            throw new JwtException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Url safe base 64 encode
     *
     * @param string $string
     *
     * @return string
     */
    private static function base64EncodeUrlSafe(string $string)
    {
        return str_replace('=', '', strtr(base64_encode($string), '+/', '-_'));
    }

    /**
     * Validate JWT token and return payload
     *
     * @param $token
     *
     * @return array
     *
     * @throws JwtException
     */
    public static function validateToken($token)
    {
        try {
            $tokenData = explode('.', $token);

            if (count($tokenData) !== 3) {
                throw new \Exception('errors.jwt.invalid');
            }

            list($header64, $payload64, $signature64) = $tokenData;

            $header = json_decode(self::base64DecodeUrlSafe($header64), true);
            $payload = json_decode(self::base64DecodeUrlSafe($payload64), true);

            if (!$header || !$payload || !isset($payload['expiration']) || Carbon::parse($payload['expiration']) < Carbon::now()) {
                throw new \Exception('errors.jwt.invalid');
            }

            $signature = self::base64DecodeUrlSafe($signature64);

            if ($signature !== hash_hmac('sha256', $header64 . '.' . $payload64, env('APP_KEY'), true)) {
                throw new \Exception('errors.jwt.invalid');
            }

            return $payload;
        } catch (\Exception $e) {
            throw new JwtException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $string
     *
     * @return bool|string
     */
    private static function base64DecodeUrlSafe($string)
    {
        $mod = strlen($string) % 4;

        if ($mod !== 0) {
            $padlen = 4 - $mod;
            $string .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($string, '-_', '+/'));
    }
}
