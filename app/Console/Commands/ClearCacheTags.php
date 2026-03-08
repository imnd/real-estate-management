<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearCacheTags extends Command
{
    protected $signature = 'cache:clear-tags {tag? : Specific tag to clear}';
    protected $description = 'Clear cache by tags';

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    public function handle()
    {
        $tag = $this->argument('tag');

        if ($tag) {
            $this->cacheService->flush([$tag]);
            $this->info("Cache for tag '$tag' cleared successfully.");
        } else {
            $this->cacheService->flushAll();
            $this->info('All tagged cache cleared successfully.');
        }
    }
}
