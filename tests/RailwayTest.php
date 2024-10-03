<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Railway;

class RailwayTest extends TestCase
{
    public function testGetVersion()
    {
        $railway = new Railway();
        $this->assertEquals('1.0.0', $railway->getVersion());
    }
}
