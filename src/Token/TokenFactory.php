<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Token;

use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Manager;
use SkinonikS\Laravel\TwoFactorAuth\Token\Token;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;
use SkinonikS\Laravel\TwoFactorAuth\Token\Traits\TokenFactoryTrait;

class TokenFactory implements TokenFactoryInterface
{
    use TokenFactoryTrait;

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Manager $manager 
     */
    public function __construct(
        protected Manager $manager,
    )
    {
        //   
    }

    /**
     * {@inheritDoc}
     */
    public function make(User $user): TokenInterface
    {
        return new Token(
            $user->getAuthIdentifier(),
            $this->getEnabledMethods($user),
            $this->getPreferredMethod($user),
        );
    }

    /**
     * @return \SkinonikS\Laravel\TwoFactorAuth\Manager 
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }
}