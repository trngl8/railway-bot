<?php

namespace App;

class User
{
    private string $phone;
    private int $id;

    private string $token;

    public function __construct(string $phone, int $id, string $token)
    {
        $this->phone = $phone;
        $this->id = $id;
        $this->token = $token;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
