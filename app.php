<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Railway;
use App\User;
use App\TgHttpClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;

if ($argc < 4) {
    echo "Use: php app.php <station_from> <station_to> <date>\n";
    exit(1);
}

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$client = HttpClient::create();

$db = new \SQLite3(__DIR__ . '/var/data/local.db', SQLITE3_OPEN_READONLY);
$db->enableExceptions(true);

$statement = $db->prepare('SELECT * FROM users WHERE id = :id');
$statement->bindValue(':id', 1, SQLITE3_INTEGER);
$users = $statement->execute();

$one = $users->fetchArray(SQLITE3_ASSOC);

$user = new User($one['phone'], $one['profile_id'], $one['key']);

$statement = $db->prepare('SELECT chat_id FROM messages WHERE user_id = :id LIMIT 1');
$statement->bindValue(':id', 1, SQLITE3_INTEGER);
$messages = $statement->execute();

$subscriber = $messages->fetchArray(SQLITE3_ASSOC);

$from = $argv[1];
$to = $argv[2];
$date = new \DateTime($argv[3]);

$bot = new Railway($client, $user);
$stationFrom = $bot->searchStations($from);
$stationTo = $bot->searchStations($to);

$seats = $bot->getAvailableSeats($date, $stationFrom[0]['id'], $stationTo[0]['id']);

if (empty($seats)) {
    echo "No available seats\n";
    exit(1);
}

$result = '';
foreach ($seats as $seat) {
    $result .= sprintf("Train %s has %d free seats in %s class\n", $seat['train'], $seat['seats'], $seat['class']);
}

$tg = new TgHttpClient($client, $_ENV['TG_TOKEN']);
$tg->sendMessage($subscriber['chat_id'], $result);
