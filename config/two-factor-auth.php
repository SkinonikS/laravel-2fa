<?php

use Carbon\CarbonInterval;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\AnyMethodCondition;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\TrustedDeviceCondition;
use SkinonikS\Laravel\TwoFactorAuth\Conditions\WhitelistCondition;
use SkinonikS\Laravel\TwoFactorAuth\Features;

return [

    /**
     * 
     */
    'defaults' => [
        'method' => 'email',
    ],

    /**
     * 
     */
    'methods' => [
        'email' => [
            'driver' => 'email',
            'refresh_in' => CarbonInterval::minute(2),
        ],
    ],

    /**
     * 
     */
    'conditions' => [
        'has_any_method' => AnyMethodCondition::class,
        'whitelist' => WhitelistCondition::class,
        'trusted_device' => TrustedDeviceCondition::class,
    ],

    /**
     * 
     */
    'features' => [
        Features::whitelist() => [
            'ips' => [
                '127.0.0.1',
                '192.168.0.0/16',
                '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
                '2001:db8:abcd:0012::0/64',
            ],
        ],
        Features::trustedDevice() => [
            'cookie' => [
                'name' => 'device_id',
                'expires_in' => null,
            ],
        ],
    ],
];