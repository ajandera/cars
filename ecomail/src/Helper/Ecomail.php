<?php
namespace Core\Ecomail\Helper;

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Ecomail {
    private $client;

    public function __construct(string $apiKey) {
        $this->client = new Client([
            'base_uri' => 'https://api2.ecomailapp.cz',
            'headers' => [
                'Content-Type' => 'application/json',
                'key' => $apiKey
            ],
            'http_errors' => false // necháme si zpracovat odpovědi sami
        ]);
    }

    private function request(string $method, string $uri, array $data = null): array {
        try {
            $options = [];
            if ($data) {
                $options['json'] = $data;
            }

            $response = $this->client->request($method, $uri, $options);

            return [
                'code' => $response->getStatusCode(),
                'body' => json_decode((string) $response->getBody(), true)
            ];
        } catch (RequestException $e) {
            return [
                'code' => $e->getCode(),
                'body' => $e->getMessage()
            ];
        }
    }

    // Získání seznamů
    public function getLists(): array {
        return $this->request('GET', '/lists');
    }

    // Bulk subscribe
    public function subscribeBulk(int $listId, array $subscribers): array {
        return $this->request('POST', "/lists/{$listId}/subscribe-bulk", [
            'subscribers' => $subscribers
        ]);
    }
}
