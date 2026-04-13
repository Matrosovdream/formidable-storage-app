<?php

namespace App\Mixins\Cache;

class CacheKeyBuilder
{
    public function __construct(protected string $prefix = '') {}

    public function make(string $namespace, array $parts): string
    {
        $tail = implode(':', array_map(fn($p) => (string) $p, $parts));
        return trim($this->prefix . $namespace . ':' . $tail, ':');
    }

    public function entryMeta(int $siteId, int $entryId): string
    {
        return $this->make('entry_meta', [$siteId, $entryId]);
    }
}
