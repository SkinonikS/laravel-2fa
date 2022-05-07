<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Token;

use SkinonikS\Laravel\TwoFactorAuth\Config;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestUser;
use SkinonikS\Laravel\TwoFactorAuth\Manager;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenFactory;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;
use Illuminate\Support\Facades\Config as LaravelConfig;

class TokenFactoryTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        LaravelConfig::set(Config::getKey(), [
            'defaults' => [
                'method' => 'email',
            ],
            'methods' => [
                'email' => [
                    'driver' => 'email',
                    'refresh_in' => \Carbon\CarbonInterval::minute(2),
                ],
            ],
        ]);
    }

    public function testFactoryCreateTokens()
    {
        $factory = $this->app[TokenFactory::class];

        $user = new TestUser();
        $user->preferred = 'email';
        $user->id = 'testid';

        $token = $factory->make($user);

        $this->assertInstanceOf(TokenInterface::class, $token);
        $this->assertSame('testid', $token->getUserAuthIdentifier());
        $this->assertSame(['email'], $token->getEnabledMethods());
        $this->assertSame('email', $token->getPreferredMethod());

        $user->preferred = null;
        $token2 = $factory->make($user);

        $this->assertNull($token2->getPreferredMethod());
    }
}