<?php

use App\Mixins\Cache\Drivers\ArrayCacheDriver;

it('puts and gets a value', function () {
    $driver = new ArrayCacheDriver();
    $driver->put('k', ['v' => 1], 60);
    expect($driver->get('k'))->toBe(['v' => 1])
        ->and($driver->has('k'))->toBeTrue();
});

it('forgets a value', function () {
    $driver = new ArrayCacheDriver();
    $driver->put('k', 'v', 60);
    $driver->forget('k');
    expect($driver->has('k'))->toBeFalse()
        ->and($driver->get('k', 'fallback'))->toBe('fallback');
});

it('remember runs callback once and caches', function () {
    $driver = new ArrayCacheDriver();
    $calls = 0;
    $cb = function () use (&$calls) { $calls++; return 'computed'; };

    expect($driver->remember('k', 60, $cb))->toBe('computed');
    expect($driver->remember('k', 60, $cb))->toBe('computed');
    expect($calls)->toBe(1);
});

it('reports its name', function () {
    expect((new ArrayCacheDriver())->name())->toBe('array');
});
