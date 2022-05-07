<?php

namespace SkinonikS\Laravel\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker\Checker;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker\CheckerInterface;
use SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDevice;
use SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDeviceInterface;
use SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist\Whitelist;
use SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist\WhitelistInterface;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenFactory;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenFactoryInterface;

class TwoFactorAuthServiceProvider extends ServiceProvider
{
    /**
     * 
     */
    public function boot()
    {
        $this->configurePublishing();
    }

    /** 
     * 
     */
    public function register()
    {
        $this->mergeConfig();
        $this->registerBindings();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function mergeConfig(): void
    {
        $configKey = Config::getKey();

        $this->mergeConfigFrom(__DIR__."/../config/{$configKey}.php", $configKey);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function configurePublishing(): void
    {
        $configKey = Config::getKey();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__."/../config/{$configKey}.php" => config_path("{$configKey}.php"),
            ], "{$configKey}-config");
        }
    }

    /** 
     * 
     */
    protected function registerBindings(): void
    {
        $this->app->singleton(Manager::class, Manager::class);
        $this->app->singleton(TokenFactoryInterface::class, TokenFactory::class);

        $this->app->singleton(CheckerInterface::class, static function ($app) {
            $checker = new Checker($app, $app[Manager::class], $app['request']);

            $app->refresh('request', $checker, 'setRequest');

            return $checker;
        });
        $this->app->alias(CheckerInterface::class, Checker::class);

        $this->app->singleton(WhitelistInterface::class, static function () {
            $config = Features::getWhitelistConfig();
            
            return new Whitelist($config['ips']);
        });
        $this->app->alias(WhitelistInterface::class, Whitelist::class);

        $this->app->singleton(TrustedDeviceInterface::class, static function ($app) {
            $config = Features::getTrustedDeviceConfig();
            
            $trusted = new TrustedDevice($app['cookie'], $app['request']);

            $app->refresh('request', $trusted, 'setRequest');

            return $trusted
                ->setCookieName($config['cookie']['name'] ?? 'device_id')
                ->setExpiresIn($config['cookie']['expires_in'] ?? null);
        });
        $this->app->alias(TrustedDeviceInterface::class, TrustedDevice::class);
    }
}