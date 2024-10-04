<?php

namespace App\Tests;

use App\User;
use App\Railway;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\TestCase;

class RailwayTest extends TestCase
{
    public function testGetVersion()
    {
        $responses = [
            new MockResponse('{"version":"1.0.0"}', []),
        ];

        $httpClient = new MockHttpClient($responses);
        $user = $this->createMock(User::class);
        $user->method('getId')
            ->willReturn(1);
        $user->method('getToken')->willReturn(sha1('token'));

        $railway = new Railway($httpClient, $user);
        $this->assertEquals('1.0.0', $railway->getVersion());
    }
}
