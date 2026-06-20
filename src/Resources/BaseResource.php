<?php

namespace MsgHub\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MsgHub\Exceptions\MsgHubException;

abstract class BaseResource
{
    public function __construct(protected Client $http) {}

    protected function get(string $uri, array $query = []): array
    {
        return $this->request('GET', $uri, ['query' => $query]);
    }

    protected function post(string $uri, array $json = []): array
    {
        return $this->request('POST', $uri, ['json' => $json]);
    }

    protected function put(string $uri, array $json = []): array
    {
        return $this->request('PUT', $uri, ['json' => $json]);
    }

    protected function delete(string $uri): array
    {
        return $this->request('DELETE', $uri);
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $uri, $options);
            $body     = (string) $response->getBody();

            return $body ? json_decode($body, true) ?? [] : [];
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $data = json_decode($body, true) ?? [];
            $msg  = $data['message'] ?? $e->getMessage();

            throw new MsgHubException($msg, $e->getResponse()->getStatusCode(), $data);
        } catch (\Throwable $e) {
            throw new MsgHubException($e->getMessage());
        }
    }
}
