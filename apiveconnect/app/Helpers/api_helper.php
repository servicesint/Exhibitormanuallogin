<?php

if (!function_exists('apiRequest')) {

    function apiRequest(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = []
    ) {

        $client = \Config\Services::curlrequest();

        $baseUrl = rtrim(env('API_BASE_URL'), '/') . '/';

        $token = session()->get('jwt_token');

        $defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        if (!empty($token)) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $token;
        }

        $options = [
            'headers' => array_merge($defaultHeaders, $headers),
            'http_errors' => false
        ];

        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            $options['json'] = $data;
        }

        if (strtoupper($method) === 'GET' && !empty($data)) {
            $options['query'] = $data;
        }

        $response = $client->request(
            strtoupper($method),
            $baseUrl . ltrim($endpoint, '/'),
            $options
        );

        return [
            'status_code' => $response->getStatusCode(),
            'data' => json_decode($response->getBody(), true),
            'raw' => $response->getBody()
        ];
    }
}

if (!function_exists('apiGet')) {

    function apiGet(
        string $endpoint,
        array $params = [],
        array $headers = []
    ) {
        return apiRequest(
            'GET',
            $endpoint,
            $params,
            $headers
        );
    }
}

if (!function_exists('apiPost')) {

    function apiPost(
        string $endpoint,
        array $data = [],
        array $headers = []
    ) {
        return apiRequest(
            'POST',
            $endpoint,
            $data,
            $headers
        );
    }
}

if (!function_exists('apiPut')) {

    function apiPut(
        string $endpoint,
        array $data = [],
        array $headers = []
    ) {
        return apiRequest(
            'PUT',
            $endpoint,
            $data,
            $headers
        );
    }
}

if (!function_exists('apiDelete')) {

    function apiDelete(
        string $endpoint,
        array $data = [],
        array $headers = []
    ) {
        return apiRequest(
            'DELETE',
            $endpoint,
            $data,
            $headers
        );
    }
}