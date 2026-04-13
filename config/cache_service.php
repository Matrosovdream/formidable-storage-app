<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Driver
    |--------------------------------------------------------------------------
    | Which CacheDriverInterface implementation to bind. Maps to the keys in
    | the "drivers" array below. Each driver delegates to a Laravel cache
    | store defined in config/cache.php.
    */
    'driver' => env('CACHE_SERVICE_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Default TTL (seconds)
    |--------------------------------------------------------------------------
    | null = forever (rely on explicit invalidation).
    */
    'ttl' => env('CACHE_SERVICE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Key Prefix
    |--------------------------------------------------------------------------
    */
    'prefix' => env('CACHE_SERVICE_PREFIX', 'fsa:'),

    /*
    |--------------------------------------------------------------------------
    | Driver Map
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        'redis' => App\Mixins\Cache\Drivers\RedisCacheDriver::class,
        'array' => App\Mixins\Cache\Drivers\ArrayCacheDriver::class,
    ],

];
