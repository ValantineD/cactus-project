<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GeocodingService
{
    public function __construct(private HttpClientInterface $client, private CacheInterface $geocodingCache)
    {
    }

    public function geocode(string $address): ?array
    {
        return $this->geocodingCache->get(
            'geocode_' . md5($address),
            function (ItemInterface $item) use ($address) {
                $item->expiresAfter(86400);

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
                    'lat' => (float)$data[0]['lat'],
                    'lng' => (float)$data[0]['lon'],
                ];
            }
        );
    }
}
