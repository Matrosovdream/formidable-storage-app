<?php

namespace App\Mixins\Cache\Drivers;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class RedisCacheDriver extends AbstractCacheDriver
{
    public function __construct(?int $defaultTtl = null, protected string $store = 'redis')
    {
        parent::__construct($defaultTtl);
    }

    protected function store(): Repository
    {
        return Cache::store($this->store);
    }

    public function name(): string
    {
        return 'redis';
    }
}
