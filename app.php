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

if ($argc < 4) {
    echo "Use: php app.php <station_from_id> <station_to_id> <date>\n";
    exit(1);
}

$station_from_id = $argv[1];
$station_to_id = $argv[2];
$date = new \DateTime($argv[3]);

$bot = new Railway($client, $user);

$seats = $bot->getAvailableSeats($date, $station_from_id, $station_to_id);

foreach ($seats as $seat) {
    echo sprintf("Train %s has %d free seats in %s class\n", $seat['train'], $seat['seats'], $seat['class']);
}
