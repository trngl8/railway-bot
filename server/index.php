<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use App\TgHttpClient;

$request = Request::createFromGlobals();

$uri = $request->getRequestUri();

$response = new JsonResponse([
    'status' => 'error',
    'message' => 'Not found',
], 404);

$steps = [
    'from' => 'Enter the departure station:',
    'to' => 'Enter the arrival station:',
    'date' => 'Enter the date of the trip:',
];

$trip = [];

if ($uri === '/bot' && $request->isMethod('POST')) {
    $client = HttpClient::create();
    $tg = new TgHttpClient($client, $_ENV['TG_TOKEN']);

    session_start();
    $currentStep = $_SESSION['current_step'] ?? 0;

    $data = $request->toArray();
    $text = $data['message']['text'];
    $chatId = $data['message']['from']['id'];

    if (str_starts_with($text, '/start')) {
        $currentStep = 0;
    }

    $db = new \SQLite3(__DIR__ . '/../var/data/local.db', SQLITE3_OPEN_READWRITE);
    $db->enableExceptions(true);
    $db->query('INSERT INTO messages("chat_id", "user_id", "body", "step") VALUES ("'.$chatId.'", "1", "'.$text.'", "'.$currentStep.'")');
    $db->close();

    $tg->sendMessage($chatId, $steps[$currentStep]);

    if ($currentStep === 0) {
        $currentStep++;
    }

    if ($currentStep === 1) {
        $trip['from'] = $text;
        $currentStep++;
    }

    if ($currentStep === 2) {
        $trip['to'] = $text;
        $currentStep++;
    }

    if ($currentStep === 3) {
        $trip['date'] = $text;
        $currentStep++;
    }

    $_SESSION['current_step'] = $currentStep;

    $response = new JsonResponse([
        'status' => 'ok',
    ]);
}

$response->send();
