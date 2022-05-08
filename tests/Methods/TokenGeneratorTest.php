<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Methods;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lcobucci\JWT\Token;
use SkinonikS\Laravel\TwoFactorAuth\Methods\TokenGenerator;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestUser;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;

class TokenGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testCustomGenerator()
    {
        $user = TestUser::factory()->create();

        TokenGenerator::use(static function () {
            return 'custom';
        });

        $this->assertSame('custom', TokenGenerator::generate($user));
    }

    public function testGeneratesToken()
    {
        $user = TestUser::factory()->create();

        TokenGenerator::reset();

        $this->assertIsNumeric(TokenGenerator::generate($user));
    }
}