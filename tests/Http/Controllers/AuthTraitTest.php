<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Http\Controllers;

use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use SkinonikS\Laravel\TwoFactorAuth\Http\Controllers\Traits\AuthTrait;
use SkinonikS\Laravel\TwoFactorAuth\Http\TwoFactorAuth;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestUser;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;

class AuthTraitTest extends TestCase
{
    use AuthTrait;
    use RefreshDatabase;

    protected bool $shouldStart = false;

    public function testShouldStartTwoFactor()
    {
        $this->flushSession();
        
        $this->shouldStart = true;

        $user = TestUser::factory()->create();

        $this->attempt($user);

        $this->assertTrue(TwoFactorAuth::isAlreadyStarted());
    }

    public function testAttempt()
    {
        $this->flushSession();

        $this->shouldStart = false;

        Event::fake();

        $user = TestUser::factory()->create();

        $this->attempt($user);

        Event::assertDispatched(LoginEvent::class);
    }

    protected function shouldStartTwoFactorAuth()
    {
        return $this->shouldStart;
    }
}