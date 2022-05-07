<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Token;

use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use SkinonikS\Laravel\TwoFactorAuth\Token\Token;

class TokenTest extends TestCase
{
    protected function createToken(): Token
    {
        return new Token(
            'user-id',
            ['email'],
            'email',
        );
    }

    public function test_token()
    {
        $token = $this->createToken();

        $this->assertSame('user-id', $token->getUserAuthIdentifier());
        $this->assertSame(['email'], $token->getEnabledMethods());
        $this->assertSame('email', $token->getPreferredMethod());
    }
}
