<?php

namespace SkinonikS\Laravel\TwoFactorAuth;

use Closure;
use InvalidArgumentException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use SkinonikS\Laravel\TwoFactorAuth\Methods\BackupCodes\BackupCodesMethod;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Authenticator;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Mail\MailMethod;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;

class Manager
{
/**
     * @var array<string, \SkinonikS\Laravel\TwoFactorAuth\Methods\Authenticator>
     */
    protected array $resolved = [];

    /**
     * @var array<string, \Closure>
     */
    protected array $customResolvers = [];

    /**
     * @param \Illuminate\Foundation\Application $app 
     */
    public function __construct(
        protected Application $app,
    )
    {
        //
    }

    /**
     * @param string|null $method 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\Authenticator 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \InvalidArgumentException 
     */
    public function method(?string $method = null): Authenticator
    {
        $method = $method ?: $this->getDefaultMethod();

        return $this->resolved[$method]
            ?? $this->resolved[$method] = $this->resolve($method);
    }

    /**
     * @return array<int, string>
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public function getMethods(): array
    {
        return array_keys(Config::get('methods'));
    }

    /**
     * @param string $name 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public function hasMethod(string $name): bool
    {
        return Config::has("methods.{$name}");
    }

    /**
     * @param string $name 
     * @param callable $resolver 
     * @return self
     */
    public function extend(string $name, callable $resolver): self
    {
        $this->customResolvers[$name] = Closure::fromCallable($resolver);

        return $this;
    }

    /**
     * @param string $name 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\Authenticator 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \InvalidArgumentException 
     */
    protected function resolve(string $name): Authenticator
    {
        $config = $this->getConfig($name);

        if (array_key_exists($config['driver'], $this->customResolvers)) {
            return $this->resolveCustom($name, $config['driver'], $config);
        }

        $method = 'create' . Str::ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->{$method}($name, $config);
        }

        throw new InvalidArgumentException("Method driver `{$config['driver']}` is not supported.");
    }

    /**
     * @param string $name 
     * @param string $driverName 
     * @param array $config 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\Authenticator 
     */
    protected function resolveCustom(string $name, string $driverName, array $config): Authenticator
    {
        $resolver = $this->customResolvers[$driverName];

        return $this->buildMethod($name, $resolver($config, $this->app));
    }

    /**
     * @param string $name 
     * @param \SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface $method 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\Authenticator 
     */
    protected function buildMethod(string $name, MethodInterface $method): Authenticator
    {
        return new Authenticator($name, $method);
    }

    /**
     * @param string $name 
     * @return array<string, string>
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \InvalidArgumentException 
     */
    protected function getConfig(string $name): array
    {
        $config = Config::get("methods.$name");

        if (!is_array($config)) {
            throw new InvalidArgumentException("Method `$name` is not defined in the configuration.");
        }

        if (!array_key_exists('driver', $config)) {
            throw new InvalidArgumentException("\"driver\" is not defined in the method `$name` configuration.");
        }

        return $config;
    }

    /**
     * @param string $name
     * @param array $config 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\Mail\Authenticator 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function createEmailDriver(string $name, array $config): Authenticator
    {
        $method = new MailMethod($this->app['session.store'], $config['refresh_in']);

        return $this->buildMethod($name, $method);
    }

    /**
     * @param string $name 
     * @param array $config 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\Mail\Authenticator 
     */
    protected function createBackupCodesDriver(string $name, array $config): Authenticator
    {
        return $this->buildMethod($name, new BackupCodesMethod());
    }

    /**
     * @return string 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public function getDefaultMethod(): string
    {
        $method = Config::get('defaults.method');

        if (!$method) {
            throw new InvalidArgumentException("Default method is not defined.");
        }

        return $method;
    }

    /**
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \InvalidArgumentException 
     */
    public function __call($name, $arguments)
    {
        return $this->method()->$name(...$arguments);
    }
}
