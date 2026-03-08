<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\CacheService;
use Closure;

trait HasCacheInvalidation
{
    protected static function bootHasCacheInvalidation(): void
    {
        // Cache invalidation on creation
        static::created(function ($model) {
            $model->invalidateCache();
        });

        // Cache invalidation on update
        static::updated(function ($model) {
            $model->invalidateCache();
        });

        // Cache invalidation on deletion
        static::deleted(function ($model) {
            $model->invalidateCache();
        });
    }

    abstract public static function created(Closure $param);

    /**
     * Invalidate cache for model
     */
    protected function invalidateCache(): void
    {
        $cacheService = app(CacheService::class);

        // Getting tags from the model (if the getCacheTags method is defined)
        $tags = method_exists($this, 'getCacheTags')
            ? $this->getCacheTags()
            : $this->getDefaultCacheTags();

        $cacheService->flush($tags);
    }

    /**
     * Get default tags
     */
    protected function getDefaultCacheTags(): array
    {
        $tags = [CacheService::TAG_STATISTICS];

        // Defining tags based on the model class
        $class = class_basename($this);

        switch ($class) {
            case 'Complex':
                $tags[] = CacheService::TAG_COMPLEXES;
                break;
            case 'Building':
                $tags[] = CacheService::TAG_BUILDINGS;
                break;
            case 'Section':
                $tags[] = CacheService::TAG_SECTIONS;
                break;
            case 'Premise':
                $tags[] = CacheService::TAG_PREMISES;
                break;
        }

        return $tags;
    }

    abstract public static function updated(Closure $param);

    abstract public static function deleted(Closure $param);
}
