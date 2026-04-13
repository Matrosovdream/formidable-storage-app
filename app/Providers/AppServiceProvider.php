<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mixins\Cache\CacheKeyBuilder;
use App\Mixins\Cache\Contracts\CacheDriverInterface;
use App\Services\CacheService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CacheDriverInterface::class, function ($app) {
            $config = $app['config']->get('cache_service');
            $driverClass = $config['drivers'][$config['driver']]
                ?? throw new \RuntimeException("Unknown cache_service driver: {$config['driver']}");

            return new $driverClass($config['ttl']);
        });

        $this->app->singleton(CacheKeyBuilder::class, function ($app) {
            return new CacheKeyBuilder($app['config']->get('cache_service.prefix', ''));
        });

        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService(
                $app->make(CacheDriverInterface::class),
                $app->make(CacheKeyBuilder::class),
                $app['config']->get('cache_service.ttl')
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
