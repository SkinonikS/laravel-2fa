<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Http\Controllers\Traits;

use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker\CheckerInterface;
use SkinonikS\Laravel\TwoFactorAuth\Http\TwoFactorAuth;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenFactoryInterface;

trait AuthTrait
{
    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $remember 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException 
     */
    protected function attempt(User $user, bool $remember = false)
    {
        $token = $this->getTokenFactory()
            ->make($user);

        if ($this->shouldStartTwoFactorAuth($token)) {
            return $this->startTwoFactorAuth($token, $remember);
        }

        Auth::login($user, $remember);
        
        $this->fireLoginEvent($user, $remember);

        return $this->authenticated($user, $remember);
    }

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface $token 
     * @param bool $remember 
     */
    protected function startTwoFactorAuth(TokenInterface $token, bool $remember = false): void
    {
        TwoFactorAuth::start($token, $remember);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $remember 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function fireLoginEvent(User $user, bool $remember = false): void
    {
        event(new LoginEvent(Auth::guard(), $user, $remember));
    }

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface $token 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function shouldStartTwoFactorAuth(TokenInterface $token): bool
    {
        return app(CheckerInterface::class)->check($token);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $remember 
     */
    protected function authenticated(User $user, bool $remember = false)
    {
        //
    }

    /**
     * @return \SkinonikS\Laravel\TwoFactorAuth\Token\TokenFactoryInterface 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function getTokenFactory(): TokenFactoryInterface
    {
        return app(TokenFactoryInterface::class);
    }
}