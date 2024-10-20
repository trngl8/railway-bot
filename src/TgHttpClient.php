<?php

namespace App;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

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

    public function sendMessage(int $chatId, string $text): ResponseInterface
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

    public function getMe(): ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'getMe');

        $response = $this->client->request('GET', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function getWebhookInfo(): ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'getWebhookInfo');

        $response = $this->client->request('GET', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function deleteWebhook(): ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'deleteWebhook');

        $response = $this->client->request('POST', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function setWebhook(string $webhookUrl): ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'setWebhook');

        $response = $this->client->request('POST', $url, [
            'body' => [
                'url' => $webhookUrl
            ]
        ]);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function setCommands(array $commands): ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'setMyCommands');

        $response = $this->client->request('POST', $url, [
            'body' => [
                'commands' => json_encode($commands)
            ]
        ]);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function deleteCommands(): ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->token, 'deleteMyCommands');

        $response = $this->client->request('POST', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \Exception(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }
}
