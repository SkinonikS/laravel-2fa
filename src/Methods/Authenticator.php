<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods;

use Closure;
use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Events\PassedEvent;
use SkinonikS\Laravel\TwoFactorAuth\Events\TokenSentEvent;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;

class Authenticator
{
    /**
     * @var null|\Closure
     */
    protected static ?Closure $passedEvent = null;

    /**
     * @var null|\Closure
     */
    protected static ?Closure $tokenSentEvent = null;

    /**
     * @param string $name 
     * @param \SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface $method 
     */
    public function __construct(
        protected string $name,
        protected MethodInterface $method,
    )
    {
        //    
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return bool 
     */
    public function enabled(User $user): bool
    {
        return $this->method->enabled($user);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return null|string 
     */
    public function sendToken(User $user): ?string
    {
        $token = $this->method->sendToken($user);

        if (!empty($token)) {
            $this->fireTokenSentEvent($user, $token);
        }

        return $token;
    }   

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param string $token 
     * @param bool $trusted 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public function verify(User $user, string $token, bool $trusted = false): bool
    {
        $verified = $this->method->verify($user, $token);

        if ($verified) {
            $this->firePassedEvent($user, $trusted);
        }

        return $verified;
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $trusted 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function firePassedEvent(User $user, bool $trusted = false): void
    {
        $event = self::$passedEvent
            ? call_user_func(self::$passedEvent, $user, $this->name, $trusted)
            : new PassedEvent($user, $this->name, $trusted);

        event($event);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param string $token 
     * @return void
     */
    protected function fireTokenSentEvent(User $user, string $token): void
    {
        $event = self::$tokenSentEvent
            ? call_user_func(self::$tokenSentEvent, $user, $token)
            : new TokenSentEvent($user, $this->name, $token);

        event($event);
    }

    /**
     * @param null|\Closure $passedEvent 
     */
    public static function usePassedEvent(?Closure $passedEvent = null): void
    {
        self::$passedEvent = $passedEvent;
    }

    /** 
     * 
     */
    public static function useDefaultPassedEvent(): void
    {
        static::usePassedEvent(null);
    }

    /**
     * @param null|\Closure $passedEvent 
     */
    public static function useTokenSentEvent(?Closure $tokenSentEvent = null): void
    {
        self::$tokenSentEvent = $tokenSentEvent;
    }

    /** 
     * 
     */
    public static function useDefaultTokenSentEvent(): void
    {
        static::useTokenSentEvent(null);
    }

    /**
     * @return \SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface 
     */
    public function getMethod(): MethodInterface
    {
        return $this->method;
    }
}