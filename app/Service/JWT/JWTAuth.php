<?php

namespace App\Service\JWT;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{
    protected string $key;
    protected array $payload = [];

    protected DateTime $timeNow;

    public function __construct()
    {
        $this->timeNow = new DateTime();
        $this->key = $this->getKeySecrect();
        $this->payload = [
            'iss' => env('HOST_NAME', ""),
            'aud' => env('HOST_NAME', ""),
            'iat' => $this->timeNow->getTimestamp(),
            'nbf' => $this->timeNow->getTimestamp(),
            'exp' => $this->timeNow->modify("+" . env("JWT_EXP", 60) . " seconds")->getTimestamp()
        ];
    }

    public function setPayload(string $name, $value = ''): void
    {
        $this->payload[$name] = $value;
    }

    public function encode(): string
    {
        return JWT::encode($this->payload, $this->key, 'HS256', null);
    }

    public function decode(string $jwt): ?\stdClass
    {
        try {
            return JWT::decode($jwt, new Key($this->key, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function refresh(string $jwt): ?string
    {
        if ($this->decode($jwt) === null) {
            return null;
        }
        return $this->encode();
    }

    /**
     * Return key secrect for encrypt jwt
     */
    protected function getKeySecrect()
    {
        $key = env("KEY_JWT", NULL);
        if ($key) {
            return $key;
        } else {
            new \Exception("KEY_JWT not set in .env", 1);
        }
    }
}