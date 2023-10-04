<?php

namespace App\Helper;

use App\Exception\Handler\AuthJWTException;
use Psr\Http\Message\ServerRequestInterface;
use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHelper
{
    protected string $key;
    protected array $payload = [];

    protected DateTime $timeNow;

    public function __construct()
    {
        $this->timeNow = new DateTime();
        $this->key = env("KEY_JWT", NULL);
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
            throw new AuthJWTException('Token Invalid', 401);
        }
    }

    public function refresh(string $jwt): ?string
    {
        if ($this->decode($jwt) === null) {
            return null;
        }
        return $this->encode();
    }

    public static function getJWTHeader(ServerRequestInterface $request): string
    {
        $headerAuth = $request->getHeader('authorization');
        $matches = '';
        if (count($headerAuth) === 0) {
            throw new AuthJWTException('authorization header not found', 401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $headerAuth[0], $matches)) {
            throw new AuthJWTException('token not found', 401);
        }

        $jwt = $matches[1];
        if (!$jwt) {
            throw new AuthJWTException('could not extract token', 401);
        }

        return $jwt;
    }
}