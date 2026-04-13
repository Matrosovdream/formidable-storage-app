<?php

use App\Mixins\Cache\CacheKeyBuilder;

it('builds entry meta key with prefix', function () {
    $keys = new CacheKeyBuilder('fsa:');
    expect($keys->entryMeta(7, 42))->toBe('fsa:entry_meta:7:42');
});

it('builds generic key without prefix', function () {
    $keys = new CacheKeyBuilder();
    expect($keys->make('thing', ['a', 1]))->toBe('thing:a:1');
});
