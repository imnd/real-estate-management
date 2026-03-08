<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CacheTtlType;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    public const TAG_COMPLEXES = 'complexes';
    public const TAG_BUILDINGS = 'buildings';
    public const TAG_SECTIONS = 'sections';
    public const TAG_PREMISES = 'premises';
    public const TAG_STATISTICS = 'statistics';
    public const TAG_FILTERS = 'filters';
    public const TAG_FLOORS = 'floors';

    public function flushAll(): void
    {
        $allTags = [
            self::TAG_COMPLEXES,
            self::TAG_BUILDINGS,
            self::TAG_SECTIONS,
            self::TAG_PREMISES,
            self::TAG_STATISTICS,
            self::TAG_FILTERS,
        ];

        $this->flush($allTags);
    }

    public function flush(array $tags): void
    {
        if ($this->supportsTags()) {
            Cache::tags($tags)->flush();
        } else {
            Log::info('Cache tags not supported, manual flush needed', ['tags' => $tags]);
            $this->flushWithoutTags();
        }
    }

    protected function supportsTags(): bool
    {
        return config('cache.supports_tags', false);
    }

    protected function flushWithoutTags(): void
    {
        Cache::flush();
        Log::warning('Cache flushed completely due to tags not supported');
    }

    public function generateFilterKey(string $baseKey, array $params): string
    {
        $hash = md5(serialize($params));
        return "{$baseKey}_$hash";
    }

    public function rememberDefault(string $key, Closure $callback, array $tags = []): mixed
    {
        return $this->remember($key, $tags, $callback, CacheTtlType::Default);
    }

    public function remember(string $key, array $tags, Closure $callback, ?CacheTtlType $ttlType = null)
    {
        $ttl = $ttlType ? $this->getTtl($ttlType) : 3600;

        if ($this->supportsTags()) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        return Cache::remember($this->buildTaggedKey($key, $tags), $ttl, $callback);
    }

    protected function getTtl(CacheTtlType $type): int
    {
        return config("cache.ttl.{$type->value}", 3600);
    }

    protected function buildTaggedKey(string $key, array $tags): string
    {
        $tagsPart = implode('|', $tags);
        return "tags[{$tagsPart}]_$key";
    }
}
