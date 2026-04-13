<?php

namespace App\Mixins\Cache\Drivers;

use Closure;
use App\Mixins\Cache\Contracts\CacheDriverInterface;

abstract class AbstractCacheDriver implements CacheDriverInterface
{
    public function __construct(protected ?int $defaultTtl = null) {}

    public function remember(string $key, ?int $ttl, Closure $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value, $ttl ?? $this->defaultTtl);

        return $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key, $sentinel = new \stdClass()) !== $sentinel;
    }

    abstract protected function store(): \Illuminate\Contracts\Cache\Repository;

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->store()->get($key, $default);
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        return $ttl === null
            ? $this->store()->forever($key, $value)
            : $this->store()->put($key, $value, $ttl);
    }

    public function forget(string $key): bool
    {
        return $this->store()->forget($key);
    }
}
