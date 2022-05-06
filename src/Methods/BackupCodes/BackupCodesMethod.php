<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods\BackupCodes;

use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasBackupCodesMethod;

class BackupCodesMethod implements MethodInterface
{
    /**
     * {@inheritDoc}
     */
    public function enabled(User $user): bool
    {
        return $user instanceof HasBackupCodesMethod && $user->isBackupCodesMethodEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function sendToken(User $user): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function verify(User $user, string $token): bool
    {
        return $user->useBackupCode($token);
    } 
}