<?php

namespace App\Libraries;

class JwtPayload
{
    private static ?object $payload = null;

    public static function set(object $payload): void
    {
        self::$payload = $payload;
    }

    public static function get(): ?object
    {
        return self::$payload;
    }
}