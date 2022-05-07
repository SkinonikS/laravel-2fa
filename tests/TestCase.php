<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests;

use SkinonikS\Laravel\TwoFactorAuth\TwoFactorAuthServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            TwoFactorAuthServiceProvider::class,
        ];
    }
}
