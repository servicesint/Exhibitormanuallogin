<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('decode_api_token')) {
    /**
     * Decode the API JWT and return its payload, or null if it is missing,
     * malformed, expired, or fails signature verification.
     *
     * The signature is verified with JWT_SECRET (the same secret the API
     * signs with). If JWT_SECRET is not configured, the payload is decoded
     * WITHOUT verification so existing logins keep working — set JWT_SECRET
     * in .env to enforce verification.
     */
    function decode_api_token(?string $token): ?array
    {
        $token = trim((string) $token);
        if ($token === '' || substr_count($token, '.') !== 2) {
            return null;
        }

        $secret = (string) env('JWT_SECRET', '');

        if ($secret !== '') {
            try {
                return (array) JWT::decode($token, new Key($secret, 'HS256'));
            } catch (\Throwable $e) {
                log_message('notice', '[decode_api_token] ' . $e->getMessage());
                return null;
            }
        }

        $payload = json_decode((string) JWT::urlsafeB64Decode(explode('.', $token)[1]), true);
        if (!is_array($payload)) {
            return null;
        }
        if (!empty($payload['exp']) && (int) $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }
}

if (!function_exists('brand_colors')) {
    /** Colours for an event / sub-event from Config\EventThemes (default < event < sub-event). */
    function brand_colors(?int $eventId = null, ?int $subEventId = null): array
    {
        $config = config('EventThemes');

        return array_merge(
            $config->default,
            $config->events[$eventId] ?? [],
            $config->subEvents[$subEventId] ?? []
        );
    }
}

if (!function_exists('brand_style')) {
    /**
     * <style> block setting the --brand-* CSS variables for an event / sub-event.
     * Put it last in <head> so it overrides the defaults in the page's own CSS.
     */
    function brand_style(?int $eventId = null, ?int $subEventId = null): string
    {
        $c = brand_colors($eventId, $subEventId);
        $rgba = static function (string $hex, float $alpha): string {
            [$r, $g, $b] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');

            return "rgba($r, $g, $b, $alpha)";
        };

        $vars = [
            'primary'        => $c['primary'],
            'primary-dark'   => $c['primary_dark'],
            'accent-bg'      => $c['accent_bg'],
            'gradient'       => "linear-gradient(to left, {$c['primary']}, {$c['primary_dark']})",
            'gradient-start' => $c['primary'],
            'gradient-end'   => $c['primary_dark'],
            'btn-bg'         => $c['primary'],
            'btn-hover'      => $c['primary_dark'],
            'btn-shadow'     => $rgba($c['primary'], 0.25),
            'input-border'   => $c['primary'],
            'input-shadow'   => $rgba($c['primary'], 0.15),
            'checkbox-bg'    => $c['primary'],
            'login-accent'   => $rgba($c['primary'], 0.15),
            'login-accent2'  => $rgba($c['primary_dark'], 0.10),
        ];

        $css = '';
        foreach ($vars as $name => $value) {
            $css .= "--brand-$name: " . preg_replace('/[^#a-zA-Z0-9(),.% -]/', '', $value) . ';';
        }

        return "<style>:root{{$css}}</style>";
    }
}

if (!function_exists('sub_event_info')) {
    /**
     * A sub-event with its parent event, or null if it does not exist.
     * Keys: sub_event_id, sub_event_name, sub_event_logo, start_date, end_date,
     * event_id, event_name, url.
     */
    function sub_event_info(int $subEventId): ?array
    {
        static $cache = [];
        if (!array_key_exists($subEventId, $cache)) {
            $cache[$subEventId] = $subEventId > 0 ? (new \App\Models\EventModel())->findSubEvent($subEventId) : null;
        }

        return $cache[$subEventId];
    }
}

if (!function_exists('current_sub_event')) {
    /** The logged-in exhibitor's sub-event (from the API token), or null. */
    function current_sub_event(): ?array
    {
        $payload = decode_api_token(current_api_token());

        return sub_event_info((int) ($payload['sub_event_id'] ?? 0));
    }
}

if (!function_exists('current_brand_style')) {
    /** brand_style() for the logged-in exhibitor's event / sub-event. */
    function current_brand_style(): string
    {
        $subEvent = current_sub_event();

        return brand_style($subEvent['event_id'] ?? null, $subEvent['sub_event_id'] ?? null);
    }
}

if (!function_exists('current_api_token')) {
    /**
     * The API token from the query string (first load), Authorization header
     * (AJAX) or api_token cookie.
     */
    function current_api_token(): string
    {
        $request = service('request');

        $token = $request->getGet('token');
        if (!$token) {
            $header = $request->getHeaderLine('Authorization');
            if (str_starts_with($header, 'Bearer ')) {
                $token = substr($header, 7);
            }
        }

        return trim((string) ($token ?: $request->getCookie('api_token')));
    }
}
