<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests;

use Illuminate\Support\Facades\Config as LaravelConfig;
use SkinonikS\Laravel\TwoFactorAuth\Config;
use SkinonikS\Laravel\TwoFactorAuth\Features;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;

class FeaturesTest extends TestCase
{
    public function testReturnsOnlyEnabledFeatures()
    {
        LaravelConfig::set(Config::getKey(), [
            'features' => [
                Features::whitelist() => [
                    'ips' => [
                        '127.0.0.1',
                    ],
                ],
                Features::trustedDevice() => [
                    'cookie' => [],
                ],
            ]
        ]);

        $this->assertSame([Features::whitelist(), Features::trustedDevice()], Features::getEnabledFeatures());

        LaravelConfig::set(Config::getKey(), [
            'features' => [
                Features::whitelist() => [
                    'ips' => [
                        '127.0.0.1',
                    ],
                ],
            ]
        ]);

        $this->assertSame([Features::whitelist()], Features::getEnabledFeatures());
    }

    public function testWhitelistFeatureEnabled()
    {
        LaravelConfig::set(Config::getKey(), [
            'features' => [
                Features::whitelist() => [
                    'ips' => [
                        '127.0.0.1',
                    ],
                ],
            ]
        ]);

        $this->assertTrue(Features::isWhitelistEnabled());
        $this->assertIsArray(Features::getWhitelistConfig());
        $this->assertArrayHasKey('ips', Features::getWhitelistConfig());
    }

    public function testTrustedDeviceFeatureEnabled()
    {
        LaravelConfig::set(Config::getKey(), [
            'features' => [
                Features::trustedDevice() => [
                    'cookie' => [],
                ],
            ]
        ]);

        $this->assertTrue(Features::isTrustedDeviceEnabled());
        $this->assertIsArray(Features::getTrustedDeviceConfig());
        $this->assertArrayHasKey('cookie', Features::getTrustedDeviceConfig());
    }
}