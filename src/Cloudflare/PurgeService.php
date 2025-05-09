<?php

namespace Cloudflare;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PurgeService
{
    protected string $zoneId;
    protected string $apiToken;
    protected Client $client;

    public function __construct(string $zoneId, string $apiToken)
    {
        $this->zoneId = $zoneId;
        $this->apiToken = $apiToken;
        $this->client = new Client([
            'base_uri' => 'https://api.cloudflare.com/client/v4/',
            'headers' => [
                'Authorization' => "Bearer {$apiToken}",
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function purgeEverything(): bool
    {
        return $this->sendRequest(['purge_everything' => true]);
    }

    public function purgeUrls(array $urls): bool
    {
        return $this->sendRequest(['files' => $urls]);
    }

    protected function sendRequest(array $payload): bool
    {
        try {
            $response = $this->client->post("zones/{$this->zoneId}/purge_cache", [
                'json' => $payload,
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['success'] ?? false;
        } catch (RequestException $e) {
            error_log('Cloudflare purge error: ' . $e->getMessage());
            return false;
        }
    }
}
