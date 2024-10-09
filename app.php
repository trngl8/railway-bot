<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Railway;
use App\User;
use App\TgHttpClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$client = HttpClient::create();

$data = [];
if (($handle = fopen(__DIR__ . '/var/storage/users.csv', "r")) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
        $data[] = $row;
    }
    fclose($handle);
}

$user = new User($data[1][0], $data[1][1], $data[1][2]);

$subscribers = [];
if (($handle = fopen(__DIR__ . '/var/storage/bot.csv', "r")) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
        if ($row[1] === '/start') {
            $subscribers[] = $row;
        }
    }
    fclose($handle);
}

if ($argc < 4) {
    echo "Use: php app.php <station_from> <station_to> <date>\n";
    exit(1);
}

$from = $argv[1];
$to = $argv[2];
$date = new \DateTime($argv[3]);

$bot = new Railway($client, $user);
$stationFrom = $bot->searchStations($from);
$stationTo = $bot->searchStations($to);

$seats = $bot->getAvailableSeats($date, $stationFrom[0]['id'], $stationTo[0]['id']);

$result = '';
foreach ($seats as $seat) {
    $result .= sprintf("Train %s has %d free seats in %s class\n", $seat['train'], $seat['seats'], $seat['class']);
}

$tg = new TgHttpClient($client, $_ENV['TG_TOKEN']);
foreach ($subscribers as $subscriber) {
    $tg->sendMessage($subscriber[0], $result);
}
