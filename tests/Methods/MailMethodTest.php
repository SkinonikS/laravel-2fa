<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Methods;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Mail\MailMethod;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestUser;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;

class MailMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function createMethod(): MailMethod
    {
        return new MailMethod($this->app['session.store'], \Carbon\CarbonInterval::minute(2));
    }

    public function testEnabledForUser()
    {
        $method = $this->createMethod();

        $user = TestUser::factory()->create();

        $this->assertTrue($method->enabled($user));
    }

    public function testTokenSent()
    {
        $this->flushSession();
        
        MailMethod::resetTokenGenerator();

        $method = $this->createMethod();
        
        $ref = new ReflectionClass(MailMethod::class);
        $keyMethod = $ref->getMethod('getName');
        $keyMethod->setAccessible(true);
        $key = $keyMethod->invoke($method);

        $user = TestUser::factory()->create();

        $token = $method->sendToken($user);

        $this->assertNotNull($token);
        $this->assertTrue(Session::has($key));

        $token2 = $method->sendToken($user);
        $this->assertNull($token2);
    }

    public function testVerify()
    {
        $this->flushSession();

        MailMethod::setTokenGenerator(static function ($user) {
            return 'code';
        });

        $method = $this->createMethod();

        $user = TestUser::factory()->create();

        $success = $method->verify($user, 'code');

        $this->assertFalse($success);

        $method->sendToken($user);
        $success = $method->verify($user, 'code');

        $this->assertTrue($success);
    }
}