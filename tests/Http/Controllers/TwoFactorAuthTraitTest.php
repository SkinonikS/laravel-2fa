<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Http\Controllers;

use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Support\Facades\Event;
use SkinonikS\Laravel\TwoFactorAuth\Events\PassedEvent;
use SkinonikS\Laravel\TwoFactorAuth\Http\Controllers\Traits\TwoFactorAuthTrait;
use SkinonikS\Laravel\TwoFactorAuth\Http\TwoFactorAuth;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use SkinonikS\Laravel\TwoFactorAuth\Token\Token;
use Illuminate\Support\Facades\Config as LaravelConfig;
use SkinonikS\Laravel\TwoFactorAuth\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Mail\MailMethod;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestUser;

class TwoFactorAuthTraitTest extends TestCase
{
    use TwoFactorAuthTrait;
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        LaravelConfig::set('auth.providers.users.model', TestUser::class);
    }

    protected function startFakeTwoFactorAuth()
    {
        $user = TestUser::factory()->create();

        $token = new Token($user->getAuthIdentifier(), ['email']);

        TwoFactorAuth::start($token);
    }

    public function testCancel()
    {
        $this->startFakeTwoFactorAuth();

        $this->assertTrue(TwoFactorAuth::isAlreadyStarted());

        $this->cancel();

        $this->assertFalse(TwoFactorAuth::isAlreadyStarted());
    }

    public function testThatCodeInvalidOnVerify()
    {
        $this->expectException(ValidationException::class);

        $this->startFakeTwoFactorAuth();

        MailMethod::resetTokenGenerator();

        LaravelConfig::set(Config::get(), [
            'methods' => [
                'email' => [
                    'driver' => 'email',
                    'refresh_in' => \Carbon\CarbonInterval::minute(2),
                ],
            ],
        ]);

        $this->sendToken('email');
        $this->verify('email', 'code');
    }

    public function testThatCodeValidOnVerify()
    {
        $this->startFakeTwoFactorAuth();

        MailMethod::setTokenGenerator(static function ($user) {
            return 'code';
        });

        Event::fake();

        $this->sendToken('email');
        $this->verify('email', 'code');

        Event::assertDispatched(PassedEvent::class);
        Event::assertDispatched(LoginEvent::class);
    }
}