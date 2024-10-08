<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$request = Request::createFromGlobals();

$uri = $request->getRequestUri();

if ($uri === '/bot' && $request->isMethod('POST')) {
    $file = __DIR__ . '/../var/storage/bot.csv';
    if (!file_exists($file)) {
        touch($file);
    }
    $data = $request->toArray();
    if (($handle = fopen($file, "a")) !== false) {
        fputcsv($handle, [$data['message']['from']['id'], $data['message']['text']]);
    }
    $response = new JsonResponse([
        'status' => 'ok',
    ]);
} else {
    $response = new JsonResponse([
        'status' => 'error',
        'message' => 'Not found',
    ], 404);
}

$response->send();
