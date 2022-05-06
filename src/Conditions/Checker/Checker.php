<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\AggregateCondition;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\AnyMethodCondition;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\ConditionInterface;
use SkinonikS\Laravel\TwoFactorAuth\Config;
use SkinonikS\Laravel\TwoFactorAuth\Features;
use SkinonikS\Laravel\TwoFactorAuth\Manager;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

class Checker implements CheckerInterface
{
    /**
     * @param \Illuminate\Foundation\Application $app 
     * @param \SkinonikS\Laravel\TwoFactorAuth\Manager $manager 
     * @param \Illuminate\Http\Request $request 
     */
    public function __construct(
        protected Application $app,
        protected Manager $manager,
        protected Request $request,
    )
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function check(TokenInterface $token): bool
    {
        $conditions = [];

        foreach (array_merge(['has_any_method'], Features::getEnabledFeatures()) as $feature) {
            if ($condition = $this->resolveCondition($feature)) {
                $conditions[] = $condition;
            }
        }

        return (new AggregateCondition($conditions))
            ->onFirstSuccess(false)
            ->shouldStart($this->request, $token);
    }

    /**
     * @param string $feature 
     * @return null|\SkinonikS\Laravel\TwoFactorAuth\Conditions\ConditionInterface 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function resolveCondition(string $feature): ?ConditionInterface
    {
        $class = match ($feature) {
            'has_any_method' => $this->getConditionClassByFeature('has_any_method', AnyMethodCondition::class),
            Features::trustedDevice() => $this->getConditionClassByFeature(Features::trustedDevice(), TrustedDeviceCondition::class),
            Features::whitelist() => $this->getConditionClassByFeature(Features::whitelist(), WhitelistCondition::class),
            default => null,
        };

        return $class
            ? $this->app->make($class)
            : null;
    }

    /**
     * @param string $name 
     * @param null|string $default 
     * @return string 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function getConditionClassByFeature(string $name, ?string $default = null): string
    {
        return Config::get("conditions.{$name}", $default);
    }

    /**
     * @param \Illuminate\Http\Request $request 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker\Checker 
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}