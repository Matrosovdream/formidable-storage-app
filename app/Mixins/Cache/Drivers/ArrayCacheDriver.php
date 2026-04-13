<?php

namespace App\Mixins\Cache\Drivers;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class ArrayCacheDriver extends AbstractCacheDriver
{
    protected function store(): Repository
    {
        return Cache::store('array');
    }

    public function name(): string
    {
        return 'array';
    }
}
