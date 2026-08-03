<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get token from query string (first load) or Authorization header (AJAX)
        $token = $request->getGet('token')
            ?? $this->getBearerToken($request)
            ?? $request->getCookie('api_token')
            ?? '';

        if (empty($token)) {
            return redirect()->to('login')->with('fail', 'Please Login First');
        }

        // Decode and validate token
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return redirect()->to('login')->with('fail', 'Invalid token.');
            }

            $payload = json_decode(base64_decode(str_pad(
                strtr($parts[1], '-_', '+/'),
                strlen($parts[1]) % 4,
                '=',
                STR_PAD_RIGHT
            )), true);

            if (!$payload || empty($payload['sub']) || empty($payload['sub_event_id'])) {
                return redirect()->to('login')->with('fail', 'Invalid token payload.');
            }

            // Check token expiry
            if (!empty($payload['exp']) && $payload['exp'] < time()) {
                return redirect()->to('login')->with('fail', 'Session expired. Please login again.');
            }
        } catch (\Exception $e) {
            log_message('error', '[AuthFilter] ' . $e->getMessage());
            return redirect()->to('login')->with('fail', 'Authentication failed.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}

    // Extract Bearer token from Authorization header
    private function getBearerToken(RequestInterface $request): ?string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return trim(substr($authHeader, 7));
        }
        return null;
    }
}
