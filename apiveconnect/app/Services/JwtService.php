<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    protected string $secret;
    protected int $expireTime;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET_KEY');
        $this->expireTime = (int) env('JWT_EXPIRE_TIME', 86400);
    }

    public function generateToken(array $data): string
    {
        $payload = [
            'iss'  => base_url(),
            'iat'  => time(),
            'exp'  => time() + $this->expireTime,
            'data' => $data
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validateToken(string $token)
    {
        return JWT::decode(
            $token,
            new Key($this->secret, 'HS256')
        );
    }
}