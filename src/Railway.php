<?php

namespace App;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Railway
{
    private HttpClientInterface $client;

    const HOST = "app.uz.gov.ua";

    const SCHEME = "https";

    private string $baseUrl;

    public function __construct(HttpClientInterface $client, User $user)
    {
        $this->client = $client->withOptions([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $user->getToken()),
                'x-user-agent' => sprintf('UZ/2 Web/1 User/%d', $user->getId()),
            ]
        ]);
        $this->baseUrl = sprintf('%s://%s', self::SCHEME, self::HOST);
    }

    private function getTrips(string $date, int $stationFrom, int $stationTo): array
    {
        $endpoint = '/api/v3/trips';
        $pattern = '%s%s?station_from_id=%d&station_to_id=%d&with_transfers=0&date=%s';
        $uri = sprintf($pattern, $this->baseUrl, $endpoint, $stationFrom, $stationTo, $date);
        $response = $this->client->request('GET', $uri);
        return $response->toArray();
    }

    public function getAvailableSeats(\DateTime $date, int $stationFrom, int $stationTo): array
    {
        $trips = $this->getTrips($date->format('Y-m-d'), $stationFrom, $stationTo);

        $seats = [];
        foreach ($trips['direct'] as $trip) {
            foreach ($trip['train']['wagon_classes'] as $wagon) {
                if ($wagon['free_seats'] > 0) {
                    $seats[] = [
                        'train' => $trip['train']['number'],
                        'class' => $wagon['id'],
                        'seats' => $wagon['free_seats'],
                    ];
                }
            }
        }
        return $seats;
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
