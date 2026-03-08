<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\PremiseFilterDTO;
use App\Models\Premise;
use App\Repositories\PremiseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use League\Flysystem\FilesystemException;
use Orchid\Attachment\File;

readonly class PremiseService
{
    public function __construct(
        private PremiseRepository $premiseRepository
    ) {
    }

    /**
     * Get premises with filters and pagination.
     */
    public function getFilteredPremises(PremiseFilterDTO $filter): LengthAwarePaginator
    {
        $cacheKey = $filter->getCacheKey();

        return Cache::tags(['premises', 'api'])->remember($cacheKey, 300, function () use ($filter) {
            return $this->premiseRepository->getFiltered($filter);
        });
    }

    public function getPremiseWithRelations(int $id): ?Premise
    {
        $cacheKey = "premise.{$id}.with-relations";

        return Cache::tags(['premises'])->remember($cacheKey, 3600, function () use ($id) {
            return $this->premiseRepository->findWithRelations(
                $id,
                ['floor.section.building.complex', 'attachments']
            );
        });
    }

    public function createPremise(array $data): Premise
    {
        // Calculate price_per_sqm if not provided
        if (!isset($data['price_per_sqm']) && isset($data['base_price'], $data['total_area'])) {
            $data['price_per_sqm'] = $data['base_price'] / $data['total_area'];
        }

        $premise = $this->premiseRepository->create($data);

        if (isset($data['attachments'])) {
            $this->syncImages($premise, $data['attachments']);
        }

        Cache::tags(['premises', 'statistics'])->flush();

        return $this->premiseRepository->findWithRelations(
            $premise->id,
            ['floor.section.building.complex', 'attachments']
        );
    }

    /**
     * @throws FilesystemException
     */
    private function syncImages(Premise $premise, array $files): void
    {
        $attachmentIds = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $attachment = (new File($file))->load();
                $attachmentIds[] = $attachment->id;
            }
        }

        if (!empty($attachmentIds)) {
            $premise->attachment()->syncWithoutDetaching($attachmentIds);
            $premise->attachment()
                ->whereIn('attachments.id', $attachmentIds)
                ->update(['group' => 'gallery']);
        }
    }

    public function updatePremise(Premise $premise, array $data): Premise
    {
        // Recalculate price_per_sqm if price or area changed
        if ((isset($data['base_price']) || isset($data['total_area']))) {
            $basePrice = $data['base_price'] ?? $premise->base_price;
            $totalArea = $data['total_area'] ?? $premise->total_area;
            $data['price_per_sqm'] = $basePrice / $totalArea;
        }

        $this->premiseRepository->update($premise, $data);

        if (isset($data['attachments'])) {
            $this->syncImages($premise, $data['attachments']);
        }

        Cache::tags(['premises', 'statistics'])->flush();

        return $this->premiseRepository->findWithRelations(
            $premise->id,
            ['floor.section.building.complex', 'attachments']
        );
    }

    public function deletePremise(Premise $premise): bool
    {
        $result = $this->premiseRepository->delete($premise);
        Cache::tags(['premises', 'statistics'])->flush();

        return $result;
    }

    public function getStatistics(): array
    {
        return Cache::tags(['statistics'])->remember('premises.statistics', 1800, function () {
            return $this->premiseRepository->getStatistics();
        });
    }

    public function find(int $id): ?Premise
    {
        return $this->premiseRepository->find($id);
    }

    public function countByStatus(string $status): int
    {
        return $this->premiseRepository->countByStatus($status);
    }

    public function getAveragePrice(): float
    {
        return $this->premiseRepository->getAveragePrice();
    }
}
