<?php

namespace App\Console;

use App\TgHttpClient;

class Webhook
{
    private TgHttpClient $tgClient;

    private array $options;

    public function __construct(TgHttpClient $tgClient, array $options)
    {
        $this->tgClient = $tgClient;
        $this->options = $options;
    }

    public function execute()
    {
        if (!isset($this->options[0])) {
            $response = $this->tgClient->getWebhookInfo();
        }

        if (isset($this->options[0])) {
            switch ($this->options[0]) {
                case 'set':
                    if (!isset($this->options[1])) {
                        echo "Option URL not found \n";
                        exit(1);
                    }
                    $response = $this->tgClient->setWebhook($this->options[1]);
                    break;
                case 'delete':
                    $response = $this->tgClient->deleteWebhook();
                    break;
                default:
                    echo "Option not found \n";
                    exit(1);
            }
        }

        print_r($response->toArray());
    }
}
