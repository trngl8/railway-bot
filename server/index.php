<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$request = Request::createFromGlobals();

$uri = $request->getRequestUri();

$response = new JsonResponse([
    'status' => 'error',
    'message' => 'Not found',
], 404);

if ($uri === '/bot' && $request->isMethod('POST')) {
    $data = $request->toArray();
    $text = $data['message']['text'];
    $chatId = $data['message']['from']['id'];
    if ($text === '/start') {
        $db = new \SQLite3(__DIR__ . '/../var/data/local.db', SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);
        $db->query('INSERT INTO messages("chat_id", "user_id", "body") VALUES ("'.$chatId.'", "1", "'.$text.'")');
        $db->close();
        $response = new JsonResponse([
            'status' => 'ok',
        ]);
    }
}

$response->send();
