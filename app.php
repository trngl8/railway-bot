<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Railway;
use App\User;

use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();

$data = [];
if (($handle = fopen(__DIR__ . '/var/storage/users.csv', "r")) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
        $data[] = $row;
    }
    fclose($handle);
}

$user = new User($data[1][0], $data[1][1], $data[1][2]);
$bot = new Railway($client, $user);

if ($argc < 3) {
    echo "Usage: php app.php <station_from_id> <station_to_id>\n";
    exit(1);
}

$station_from_id = $argv[1];
$station_to_id = $argv[2];

$result = $bot->getTrips(new \DateTime('2024-10-11'), $station_from_id, $station_to_id);
foreach ($result['direct'] as $trip) {
    foreach ($trip['train']['wagon_classes'] as $wagon) {
        if ($wagon['free_seats'] > 0) {
            echo sprintf("Train %s has %d free seats in %s class\n", $trip['train']['number'], $wagon['free_seats'], $wagon['id']);
        }
    }
}

