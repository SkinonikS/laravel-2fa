<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests;

use InvalidArgumentException;
use SkinonikS\Laravel\TwoFactorAuth\Config;
use SkinonikS\Laravel\TwoFactorAuth\Manager;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Mail\MailMethod;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;
use SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks\TestMethod;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;
use Illuminate\Support\Facades\Config as LaravelConfig;

class ManagerTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        LaravelConfig::set(Config::getKey(), [
            'defaults' => [
                'method' => 'email',
            ],
            'methods' => [
                'testdrivernotsupported' => [
                    'driver' => 'notsupported',
                ],
                'testnodriver' => [
                    //
                ],
                'newmethod' => [
                    'driver' => 'testdriver',
                ],
                'email' => [
                    'driver' => 'email',
                    'refresh_in' => \Carbon\CarbonInterval::minute(2),
                ],
            ],
        ]);
    }

    public function testManagerResolveMethodsProperly()
    {
        $method = $this->app[Manager::class]->method('email');

        $this->assertInstanceOf(MethodInterface::class, $method);
    }

    public function testManagerResolveMethodsWithCustomDriver()
    {
        $manager = $this->app[Manager::class];

        $manager->extend('testdriver', static function () {
            return new TestMethod();
        });

        $method = $manager->method('newmethod');

        $this->assertInstanceOf(MethodInterface::class, $method);
    }

    public function testManagerChecksThatMethodExistsInTheConfiguration()
    {
        $manager = $this->app[Manager::class];

        $this->assertTrue($manager->hasMethod('email'));
    }

    public function testManagerResolveDefaultMethod()
    {
        $manager = $this->app[Manager::class];

        $this->assertSame('email', $manager->getDefaultMethod());
        $this->assertInstanceOf(MailMethod::class, $manager->method());
    }

    public function testExceptionIsThrownWhenNoDefaultMethodIsDefined()
    {
        LaravelConfig::set(Config::getKey(), [
            'defaults' => [
                'method' => null,
            ],
        ]);

        $this->expectException(InvalidArgumentException::class);

        $manager = $this->app[Manager::class];
        $manager->getDefaultMethod();
    }

    public function testManagerReturnsAllDefinedMethods()
    {
        $manager = $this->app[Manager::class];

        $this->assertIsArray($manager->getMethods());
        $this->assertContains('testdrivernotsupported', $manager->getMethods());
        $this->assertContains('testnodriver', $manager->getMethods());
        $this->assertContains('newmethod', $manager->getMethods());
        $this->assertContains('email', $manager->getMethods());
    }

    public function testExceptionIsThrownWhenUndefinedMethodIsResolving()
    {
        $this->expectException(InvalidArgumentException::class);
    
        $manager = $this->app[Manager::class];
        $manager->method('testnodriver');
    }

    public function testExceptionIsThrownWhenUnsupportedDriverIsDefinedInsideConfiguration()
    {
        $this->expectException(InvalidArgumentException::class);
    
        $manager = $this->app[Manager::class];
        $manager->method('testdrivernotsupported');
    }

    public function testExceptionIsThrownWhenMethodIsNotConfigured()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $manager = $this->app[Manager::class];
        $manager->method('testname123');
    }
}