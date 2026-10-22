<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('custom');

        $token = current_api_token();
        if ($token === '') {
            return redirect()->to('login')->with('fail', 'Please Login First');
        }

        $payload = decode_api_token($token);
        if (!$payload || empty($payload['sub']) || empty($payload['sub_event_id'])) {
            return redirect()->to('login')->with('fail', 'Session expired. Please login again.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
