<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Http;

use Illuminate\Support\Facades\Session;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

class TwoFactorAuth
{
    /**
     * @return bool 
     */
    public static function isAlreadyStarted(): bool
    {
        return Session::has(static::getKey());
    }

    /** 
     * 
     */
    public static function cancel(): void
    {
        Session::forget(static::getKey());
    }

    /**
     * @return array 
     * @throws \Illuminate\Http\Exceptions\HttpResponseException 
     * @throws \Illuminate\Http\Exceptions\HttpResponseException 
     */
    public static function getPayload(): array
    {
        return Session::get(static::getKey(), []);
    }

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface $token 
     * @param bool $rememberM
     * @return array 
     */
    public static function start(TokenInterface $token, bool $remember = false): array
    {
        Session::flush();

        Session::put(
            static::getKey(), $payload = static::generatePayload($token, $remember)
        );

        return $payload;
    }

    /**
     * @return string 
     */
    public static function getKey(): string
    {
        return 'two-factor-auth_metadata_'.sha1(static::class);
    }

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface $token 
     * @param bool $rememberMe 
     * @return array 
     */
    protected static function generatePayload(TokenInterface $token, bool $rememberMe = false): array
    {
        return [
            'enabled_methods' => $token->getEnabledMethods(),
            'preferred_method' => $token->getPreferredMethod(),
            'user_id' => $token->getUserAuthIdentifier(),
            'remember' => $rememberMe,
        ];
    }
}