<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Features;

use SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist\Whitelist;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;

class WhitelistTest extends TestCase
{
    public function createWhitelist(): Whitelist
    {
        return new Whitelist([
            '127.0.0.1',
        ]);
    }
    
    public function testIsWhitelisted()
    {
        $whitelist = $this->createWhitelist();

        $this->assertTrue($whitelist->isWhitelisted('127.0.0.1'));
        $this->assertFalse($whitelist->isWhitelisted('test'));
    }
}