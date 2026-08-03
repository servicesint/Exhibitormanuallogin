<?php

namespace App\Services;

class ApiService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();
        $this->baseUrl = env('API_BASE_URL');
    }

    public function request(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = []
    ) {
        try {

            $options = [
                'headers' => array_merge([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ], $headers)
            ];

            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->client->request(
                strtoupper($method),
                $this->baseUrl . $endpoint,
                $options
            );

            return [
                'status' => true,
                'code'   => $response->getStatusCode(),
                'data'   => json_decode($response->getBody(), true)
            ];

        } catch (\Throwable $e) {

            return [
                'status'  => false,
                'message' => $e->getMessage()
            ];
        }
    }
}