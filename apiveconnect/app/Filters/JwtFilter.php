<?php

namespace App\Filters;

use App\Libraries\JwtPayload;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'message' => 'Token missing.']);
        }

        $token = trim(substr($authHeader, 7));

        try {
            $decoded = decodeJwt($token);
            JwtPayload::set($decoded); // ✅ store in static library
        } catch (\Firebase\JWT\ExpiredException $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'message' => 'Token expired.']);
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'message' => 'Invalid token: ' . $e->getMessage()]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}