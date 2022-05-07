<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks;

use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;

class TestMethod implements MethodInterface
{
    public function enabled(User $user): bool
    {
        return true;   
    }

    public function sendToken(User $user): ?string
    {
        return null;
    }

    public function verify(User $user, string $token): bool
    {
        return true;
    }
}
