<?php

namespace App\Mixins\Cache\Contracts;

use Closure;

interface CacheDriverInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value, ?int $ttl = null): bool;

    public function remember(string $key, ?int $ttl, Closure $callback): mixed;

    public function rememberTracked(string $key, ?int $ttl, Closure $callback): array;

    public function forget(string $key): bool;

    public function has(string $key): bool;

    public function name(): string;
}
