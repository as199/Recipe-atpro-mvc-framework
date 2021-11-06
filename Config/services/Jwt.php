<?php
namespace Atpro\mvc\Config\services;

use DateTime;
use JsonException;

class Jwt
{
   

    /**
     * Generation JWT
     * @author Assane Dione <atpro0290@gmail.com>
     * @param array $header Header du token
     * @param array $payload Payload du Token
     * @param bool $state
     * @return string Token
     * @throws JsonException
     */
    public function generate(array $header, array $payload, bool $state = true): string
    {
        $validity = $_ENV['TOKEN_TTL'] ??86400;
        if ($state) {
            $now = new DateTime();
            $expiration = $now->getTimestamp() + $validity;
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }
        $base64Header = base64_encode(json_encode($header, JSON_THROW_ON_ERROR));
        $base64Payload = base64_encode(json_encode($payload, JSON_THROW_ON_ERROR));
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);
        $secret = base64_encode($_ENV['SECRET']);
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);
        $base64Signature = base64_encode($signature);
        $signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);
        return $base64Header . '.' . $base64Payload . '.' . $signature;
    }

    /**
     * Verification du token
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $token Token à Verify
     * @return bool Verify ou non
     * @throws JsonException
     */
    public function check(string $token): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        $verifToken = $this->generate($header, $payload, false);
        return $token === $verifToken;
    }

    /**
     * Récupère le header
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $token Token
     * @return array Header
     * @throws JsonException
     */
    public function getHeader(string $token): array
    {
        $array = explode('.', $token);
        return json_decode(base64_decode($array[0]), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Retourne le payload
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $token Token
     * @return array Payload
     * @throws JsonException
     */
    public function getPayload(string $token): array
    {
        $array = explode('.', $token);
        return json_decode(base64_decode($array[1]), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Verification de expiration
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $token Token à verifier
     * @return bool Verify ou non
     * @throws JsonException
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new DateTime();
        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * Verification de la validity du token
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $token Token à verifier
     * @return bool Verify ou non
     */
    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }
}
