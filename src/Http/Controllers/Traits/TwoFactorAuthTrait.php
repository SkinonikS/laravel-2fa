<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Http\Controllers\Traits;

use InvalidArgumentException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use SkinonikS\Laravel\TwoFactorAuth\Exceptions\NotStartedException;
use SkinonikS\Laravel\TwoFactorAuth\Http\TwoFactorAuth;
use SkinonikS\Laravel\TwoFactorAuth\Manager;

trait TwoFactorAuthTrait
{
    /**
     * 
     */
    protected function cancel()
    {
        TwoFactorAuth::cancel();

        return $this->canceled();
    }

    /**
     * 
     */
    protected function canceled()
    {
        //
    }

    /**
     * @param string $method 
     * @throws \Illuminate\Http\Exceptions\HttpResponseException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException 
     */
    protected function sendToken(string $method)
    {
        $user = $this->fetchUser(
            $this->getPayload()
        );

        if (!$this->getManager()
            ->method($method)
            ->enabled($user)
        ) {
            return $this->methodDisabled($method, 'sendToken');
        }

        $token = $this->getManager()
            ->method($method)
            ->sendToken($user);
            
        return $this->tokenSent($user, $method, $token);
    }

    /**
     * @param string $method 
     * @param string $action 
     */
    protected function methodDisabled(string $method, string $action)
    {
        //
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param string $method 
     * @param null|string $token 
     */
    protected function tokenSent(User $user, string $method, ?string $token = null)
    {
        //
    }

    /**
     * @param string $method 
     * @param string $token 
     * @param bool $trusted
     * @throws \Illuminate\Http\Exceptions\HttpResponseException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Psr\Container\NotFoundExceptionInterface 
     * @throws \Psr\Container\ContainerExceptionInterface 
     * @throws \Illuminate\Validation\ValidationException 
     * @throws \InvalidArgumentException
     */
    protected function verify(string $method, string $token, bool $trusted = false)
    {
        $user = $this->fetchUser(
            $payload = $this->getPayload()
        );

        if (!$this->getManager()
            ->method($method)
            ->enabled($user)
        ) {
            return $this->methodDisabled($method, 'verify');
        }

        $verified = $this->getManager()
            ->method($method)
            ->verify($user, $token, $trusted);

        if (!$verified) {
            return $this->invalidTwoFactorAuthToken($token);
        }

        Auth::login($user, $payload['remember'] ?? false);

        return $this->authenticated($user, $payload);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param array $payload 
     */
    protected function authenticated(User $user, array $payload)
    {
        TwoFactorAuth::cancel();
    }

    /**
     * @param array $payload 
     * @return \Illuminate\Foundation\Auth\User 
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException 
     */
    protected function fetchUser(array $payload): User
    {
        foreach (Config::get('auth.providers') as $provider) {
            if ($provider['driver'] === 'eloquent') {
                $model = $provider['model'];
                return $model::findOrFail($payload['user_id']);
            }
        }

        throw new InvalidArgumentException('Guard driver with name `eloquent` not found.');
    }

    /**
     * @return array 
     * @throws \SkinonikS\Laravel\TwoFactorAuth\Exceptions\NotStartedException 
     */
    protected function getPayload(): array
    {
        $payload = TwoFactorAuth::getPayload();

        if (empty($payload)) {
            throw new NotStartedException('TwoFactorAuth not started.');
        }

        return $payload;
    }

    /**
     * @param string $token 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Psr\Container\NotFoundExceptionInterface 
     * @throws \Psr\Container\ContainerExceptionInterface 
     * @throws \Illuminate\Validation\ValidationException 
     */
    protected function invalidTwoFactorAuthToken(string $token)
    {
        throw ValidationException::withMessages([ 'token' => __('auth.two_factor_auth.token'), ]);
    }

    /**
     * @return \SkinonikS\Laravel\TwoFactorAuth\Manager 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function getManager(): Manager
    {
        return app(Manager::class);
    }
}