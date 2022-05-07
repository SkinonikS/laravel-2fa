<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Features;

use Illuminate\Support\Facades\Route;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use Illuminate\Support\Facades\Cookie as CookieJar;
use SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDevice;

class TrustedDeviceTest extends TestCase
{
    public function testCookieAttached()
    {
        $trustedDevice = $this->app[TrustedDevice::class]->setCookieName('cookieName');
        $trustedDevice->attachCookie('test user agent');

        $this->assertTrue(CookieJar::hasQueued('cookieName'));
    }

    public function testDetermineThatCookieExistsInRequest()
    {
        Route::get('/test/cookie', function () {
            $trustedDevice = $this->app[TrustedDevice::class]->setCookieName('cookieName');

            if ($trustedDevice->isCookieExists()) {
                return response('ok');
            }
            
            return response('not ok');
        });

        $response = $this
            ->withCookie('cookieName', 'test')
            ->get('/test/cookie');

        $this->assertSame('ok', $response->content());
    }
}
