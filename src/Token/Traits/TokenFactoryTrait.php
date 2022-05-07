<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Token\Traits;

use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Manager;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasMethodPreferrence;

trait TokenFactoryTrait
{
    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function getEnabledMethods(User $user): array
    {
        $manager = $this->getManager();

        return collect($manager->getMethods())
            ->mapWithKeys(static function ($name) use ($manager) {
                return [$name => $manager->method($name)];
            })->filter(static function (MethodInterface $method) use ($user) {
                return $method->enabled($user);
            })->keys()->all();
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return null|string 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function getPreferredMethod(User $user): ?string
    {
        if ($user instanceof HasMethodPreferrence) {
            $method = $user->getPreferredTwoFactorAuthMethod();

            if (
                $method
                && $this->getManager()->hasMethod($method)
                && $this->getManager()->method($method)->enabled($user)
            ) {
                return $method;
            }
        }

        return null;
    }

    /**
     * @return \SkinonikS\Laravel\TwoFactorAuth\Manager 
     */
    abstract public function getManager(): Manager;
}