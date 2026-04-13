<?php

use App\Mixins\Cache\CacheKeyBuilder;
use App\Mixins\Cache\Drivers\ArrayCacheDriver;
use App\Services\CacheService;

function makeCacheService(): CacheService
{
    return new CacheService(new ArrayCacheDriver(), new CacheKeyBuilder('fsa:'));
}

it('rememberEntryMeta computes once and serves from cache', function () {
    $cache = makeCacheService();
    $calls = 0;
    $payload = ['fields' => [['id' => 1, 'value' => 'x']]];

    $r1 = $cache->rememberEntryMeta(10, 99, function () use (&$calls, $payload) {
        $calls++;
        return $payload;
    });
    $r2 = $cache->rememberEntryMeta(10, 99, function () use (&$calls) {
        $calls++;
        return ['SHOULD_NOT_BE_USED'];
    });

    expect($r1)->toBe($payload)
        ->and($r2)->toBe($payload)
        ->and($calls)->toBe(1);
});

it('forgetEntryMeta invalidates the cached payload', function () {
    $cache = makeCacheService();
    $cache->rememberEntryMeta(1, 2, fn() => 'first');

    $cache->forgetEntryMeta(1, 2);

    $rebuilt = $cache->rememberEntryMeta(1, 2, fn() => 'second');
    expect($rebuilt)->toBe('second');
});

it('isolates cache per (siteId, entryId) pair', function () {
    $cache = makeCacheService();
    $cache->rememberEntryMeta(1, 2, fn() => 'A');
    $cache->rememberEntryMeta(1, 3, fn() => 'B');
    $cache->rememberEntryMeta(2, 2, fn() => 'C');

    expect($cache->rememberEntryMeta(1, 2, fn() => 'x'))->toBe('A')
        ->and($cache->rememberEntryMeta(1, 3, fn() => 'x'))->toBe('B')
        ->and($cache->rememberEntryMeta(2, 2, fn() => 'x'))->toBe('C');
});
