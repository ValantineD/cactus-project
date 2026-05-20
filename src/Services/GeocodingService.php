<?php
namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    public function __construct(private HttpClientInterface $client) {}

    public function geocode(string $address): ?array
    {
        $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'query' => [
                'format' => 'json',
                'q' => $address,
                'limit' => 1,
            ],
            'headers' => [
                'User-Agent' => 'CactusApp/1.0'
            ]
        ]);

        $data = $response->toArray();

        if (empty($data)) {
            return null;
        }

        return [
            'lat' => (float) $data[0]['lat'],
            'lng' => (float) $data[0]['lon'],
        ];
    }
}
