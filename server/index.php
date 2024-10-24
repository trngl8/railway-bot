<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;
use App\TgHttpClient;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$request = Request::createFromGlobals();

$uri = $request->getRequestUri();

$response = new JsonResponse([
    'status' => 'error',
    'message' => 'Not found',
], 404);

$steps = [
    'Enter the departure station:',
    'Enter the arrival station:',
    'Enter the date of the trip:',
    'Searching for available seats...',
];

$trip = [];

if ($uri === '/bot' && $request->isMethod('POST')) {
    $client = HttpClient::create();
    $tg = new TgHttpClient($client, $_ENV['TG_TOKEN']);

    $data = $request->toArray();
    $text = $data['message']['text'];
    $chatId = $data['message']['from']['id'];

    $db = new \SQLite3(__DIR__ . '/../var/data/local.db', SQLITE3_OPEN_READWRITE);
    $db->enableExceptions(true);
    $statement = $db->prepare('SELECT step FROM messages WHERE chat_id = :chat_id ORDER BY id DESC LIMIT 1');
    $statement->bindValue(':chat_id', $chatId, SQLITE3_INTEGER);
    $res = $statement->execute();

    $record = $res->fetchArray(SQLITE3_ASSOC);
    $currentStep = $record['step'] ?? 0;

    if (str_starts_with($text, '/start')) {
        $currentStep = 0;
    }

    $tg->sendMessage($chatId, $steps[$currentStep]);

    switch ($currentStep) {
        case 0:
            $trip['from'] = $text;
            $currentStep++;
            break;
        case 1:
            $trip['to'] = $text;
            $currentStep++;
            break;
        case 2:
            $trip['date'] = $text;
            $currentStep++;
            break;
        default:
            break;
    }

    $db->query('INSERT INTO messages("chat_id", "user_id", "body", "step") VALUES ("'.$chatId.'", "1", "'.$text.'", "'.$currentStep.'")');
    $db->close();

    $response = new JsonResponse([
        'status' => 'ok',
    ]);
}

$response->send();
