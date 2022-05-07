<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Listeners;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use SkinonikS\Laravel\TwoFactorAuth\Events\PassedEvent;
use SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDevice;
use SkinonikS\Laravel\TwoFactorAuth\Listeners\AttachTrustedDeviceCookie;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestUser;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use Illuminate\Support\Facades\Config as LaravelConfig;
use SkinonikS\Laravel\TwoFactorAuth\Config;
use SkinonikS\Laravel\TwoFactorAuth\Features;

class AttachTrustedDeviceCookieTest extends TestCase
{
    use RefreshDatabase;

    public function testListenerWithTrustedDeviceFeature()
    {
        LaravelConfig::set(Config::getKey(), [
            'features' => [
                Features::trustedDevice() => [
                    'cookie' => [
                        'name' => 'cookieName',
                        'expires_in' => null,
                    ],
                ],
            ]
        ]);

        $user = TestUser::factory()->create();

        $this->app[AttachTrustedDeviceCookie::class]
            ->handle(new PassedEvent($user, true));

        $this->assertTrue(Cookie::hasQueued('cookieName'));
    }

    public function testListenerWhenNotTrustedAndTrustedDeviceFeatureIsEnabled()
    {
        LaravelConfig::set(Config::getKey(), [
            'features' => [
                Features::trustedDevice() => [
                    'cookie' => [
                        'name' => 'cookieName',
                        'expires_in' => null,
                    ],
                ],
            ]
        ]);

        $user = TestUser::factory()->create();

        $this->app[AttachTrustedDeviceCookie::class]
            ->handle(new PassedEvent($user, false));

        $this->assertFalse(Cookie::hasQueued('cookieName'));
    }

    public function testListenerWhenNotTrustedAndTrustedDeviceFeatureIsDisabled()
    {
        LaravelConfig::set(Config::getKey(), [
            'features' => []
        ]);

        $user = TestUser::factory()->create();

        $this->app[AttachTrustedDeviceCookie::class]
            ->handle(new PassedEvent($user, true));

        $this->assertFalse(Cookie::hasQueued('cookieName'));
    }
}