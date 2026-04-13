<?php

namespace App\Services;

use Closure;
use App\Mixins\Cache\CacheKeyBuilder;
use App\Mixins\Cache\Contracts\CacheDriverInterface;

class CacheService
{
    public function __construct(
        protected CacheDriverInterface $driver,
        protected CacheKeyBuilder $keys,
        protected ?int $defaultTtl = null
    ) {}

    public function driver(): CacheDriverInterface
    {
        return $this->driver;
    }

    public function keys(): CacheKeyBuilder
    {
        return $this->keys;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver->get($key, $default);
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        return $this->driver->put($key, $value, $ttl ?? $this->defaultTtl);
    }

    public function remember(string $key, ?int $ttl, Closure $callback): mixed
    {
        return $this->driver->remember($key, $ttl ?? $this->defaultTtl, $callback);
    }

    public function rememberTracked(string $key, ?int $ttl, Closure $callback): array
    {
        return $this->driver->rememberTracked($key, $ttl ?? $this->defaultTtl, $callback);
    }

    public function forget(string $key): bool
    {
        return $this->driver->forget($key);
    }

    public function rememberEntryMeta(int $siteId, int $entryId, Closure $callback): mixed
    {
        return $this->remember($this->keys->entryMeta($siteId, $entryId), null, $callback);
    }

    public function rememberEntryMetaTracked(int $siteId, int $entryId, Closure $callback): array
    {
        return $this->rememberTracked($this->keys->entryMeta($siteId, $entryId), null, $callback);
    }

    public function forgetEntryMeta(int $siteId, int $entryId): bool
    {
        return $this->forget($this->keys->entryMeta($siteId, $entryId));
    }
}
