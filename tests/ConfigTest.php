<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests;

use Illuminate\Support\Facades\Config as LaravelConfig;
use SkinonikS\Laravel\TwoFactorAuth\Config;
use SkinonikS\Laravel\TwoFactorAuth\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function testConfigReturnsRootConfiguration()
    {
        LaravelConfig::set(Config::getKey(), [
            'methods' => [

            ],
            'features' => [

            ],
        ]);

        $this->assertIsArray(Config::get());
        $this->assertArrayHasKey('methods', Config::get());
        $this->assertArrayHasKey('features', Config::get());
    }
}