<?php

namespace App;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class TgHttpClient
{
    const BASE_URI_PATTERN = 'https://api.telegram.org/bot%s/%s';

    private HttpClientInterface $client;

    private string $token;

    public function __construct(HttpClientInterface $client, string $telegramBotToken)
    {
        $this->client = $client;
        $this->token = $telegramBotToken;
    }

    public function sendMessage(int $chatId, string $text)
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'sendMessage');

        $response = $this->client->request('POST', $url, [
            'body' => [
                'chat_id' => $chatId,
                'text' => $text
            ],
            'extra' => ['trace_content' => true]
        ]);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }
}
