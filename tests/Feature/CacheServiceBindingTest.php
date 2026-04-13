<?php

use App\Mixins\Cache\CacheKeyBuilder;
use App\Mixins\Cache\Contracts\CacheDriverInterface;
use App\Mixins\Cache\Drivers\ArrayCacheDriver;
use App\Services\CacheService;

beforeEach(function () {
    config()->set('cache_service.driver', 'array');
    config()->set('cache_service.prefix', 'fsa:');
    config()->set('cache_service.ttl', 60);
    app()->forgetInstance(CacheDriverInterface::class);
    app()->forgetInstance(CacheKeyBuilder::class);
    app()->forgetInstance(CacheService::class);
});

it('resolves CacheService with configured driver', function () {
    $svc = app(CacheService::class);

    expect($svc)->toBeInstanceOf(CacheService::class)
        ->and($svc->driver())->toBeInstanceOf(ArrayCacheDriver::class)
        ->and($svc->keys()->entryMeta(1, 2))->toBe('fsa:entry_meta:1:2');
});

it('errors on unknown driver', function () {
    config()->set('cache_service.driver', 'nope');
    app()->forgetInstance(CacheDriverInterface::class);
    app()->forgetInstance(CacheService::class);

    app(CacheService::class);
})->throws(RuntimeException::class);
