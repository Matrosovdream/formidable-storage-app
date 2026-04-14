<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class QueueStatsService
{
    public const TYPE_FIELDS         = 'fields';
    public const TYPE_EMAILS         = 'emails';
    public const TYPE_ENTRY_HISTORY  = 'entry_history';

    private const HASH_KEY = 'queue-stats';

    public static function increment(int $siteId, string $type): void
    {
        self::safely(fn () => Redis::hincrby(self::HASH_KEY, self::field($siteId, $type), 1));
    }

    public static function decrement(int $siteId, string $type): void
    {
        self::safely(function () use ($siteId, $type) {
            $new = Redis::hincrby(self::HASH_KEY, self::field($siteId, $type), -1);
            if ((int) $new < 0) {
                Redis::hset(self::HASH_KEY, self::field($siteId, $type), 0);
            }
        });
    }

    public static function countsForSite(int $siteId): array
    {
        $values = self::safely(function () use ($siteId) {
            $fields = array_map(
                fn ($t) => self::field($siteId, $t),
                [self::TYPE_FIELDS, self::TYPE_EMAILS, self::TYPE_ENTRY_HISTORY]
            );
            return Redis::hmget(self::HASH_KEY, $fields);
        }) ?? [];

        return [
            'queued_fields_count'        => (int) ($values[0] ?? 0),
            'queued_emails_log_count'    => (int) ($values[1] ?? 0),
            'queued_entry_history_count' => (int) ($values[2] ?? 0),
        ];
    }

    private static function field(int $siteId, string $type): string
    {
        return "{$siteId}:{$type}";
    }

    private static function safely(callable $fn)
    {
        try {
            return $fn();
        } catch (Throwable $e) {
            Log::warning('QueueStatsService redis call failed: ' . $e->getMessage());
            return null;
        }
    }
}
