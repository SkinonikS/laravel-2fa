<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Conditions\Checker;

use SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker\Checker;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use SkinonikS\Laravel\TwoFactorAuth\Token\Token;

class CheckerTest extends TestCase
{
    public function testConditionsCheck()
    {
        $token = new Token('userid', ['email']);

        $checker = $this->app[Checker::class];
        $result = $checker->check($token);

        $this->assertTrue($result);
    }
}