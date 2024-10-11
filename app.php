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

$db = new \SQLite3(__DIR__ . '/var/data/local.db', SQLITE3_OPEN_READONLY);
$db->enableExceptions(true);

$statement = $db->prepare('SELECT * FROM users WHERE id = :id');
$statement->bindValue(':id', 1, SQLITE3_INTEGER);

$messages = $statement->execute();

$list = [];
$item = $messages->fetchArray(SQLITE3_ASSOC);
$db->close();

$user = new User($item['phone'], $item['profile_id'], $item['key']);

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
