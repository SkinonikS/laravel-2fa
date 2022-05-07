<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods\Traits;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\InteractsWithTime;

trait HasCooldownTrait
{
    use InteractsWithTime;

    /**
     * @return bool 
     */
    protected function canPrepare(): bool
    {
        $payload = $this->getPayload();

        if (!$payload || !array_key_exists('refresh_at', $payload) || (Carbon::now()->getTimestamp() >= $payload['refresh_at'])) {
            return true;
        }

        return false;
    }

    /**
     * @param array $payload 
     * @return array 
     * @throws \Carbon\Exceptions\UnitException 
     * @throws \Carbon\Exceptions\UnitException 
     */
    protected function mergePayload(array $payload): array
    {
        return array_merge($payload, [
            'refresh_at' => $this->availableAt($this->getRefreshIn()),
        ]);
    }

    /**
     * @return \Carbon\CarbonInterval 
     */
    abstract protected function getRefreshIn(): CarbonInterval;

    /**
     * @return null|array 
     */
    abstract protected function getPayload(): ?array;
}