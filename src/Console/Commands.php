<?php

namespace App\Console;

use App\TgHttpClient;

class Commands
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
        $response = $this->tgClient->deleteCommands();
        print_r($response->toArray());

        $newCommands = [
            [
                'command' => 'start',
                'description' => 'Start',
            ],
            [
                'command' => 'help',
                'description' => 'Help',
            ],
            [
                'command' => 'settings',
                'description' => 'Settings',
            ],
        ];
        $response = $this->tgClient->setCommands($newCommands);
        print_r($response->toArray());
    }
}
