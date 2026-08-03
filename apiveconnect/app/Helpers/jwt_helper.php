<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJwt(object $user, int $subEventId): string
{
    $key     = env('JWT_SECRET_KEY');
    $payload = [
        'iss'         => env('API_BASE_URL'),   // issuer
        'iat'         => time(),                 // issued at
        'exp'         => time() + (60 * 60 * 24), // 24 hours
        'sub'         => $user->id,
        'email'       => $user->email ?? '',
        'mobile'      => $user->mobile ?? $user->mobile_number ?? '',
        'sub_event_id'=> $subEventId,
        'exhibitor_id'=> $user->exhibitor_id ?? null,
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function decodeJwt(string $token): object
{
    return JWT::decode($token, new Key(env('JWT_SECRET_KEY'), 'HS256'));
}