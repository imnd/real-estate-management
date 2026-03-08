<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\PremiseFilterDTO;
use App\Models\Premise;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PremiseRepository
{
    public function findWithRelations(int $id, array $relations = []): ?Premise
    {
        return Premise::with($relations)->find($id);
    }

    public function find(int $id): ?Premise
    {
        return Premise::find($id);
    }

    public function create(array $data): Premise
    {
        return Premise::create($data);
    }

    public function update(Premise $premise, array $data): bool
    {
        return $premise->update($data);
    }

    public function delete(Premise $premise): bool
    {
        return $premise->delete();
    }

    public function countByStatus(string $status): int
    {
        return Premise::where('status', $status)->count();
    }

    public function count(): int
    {
        return Premise::count();
    }

    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'by_status' => $this->getCountByStatus(),
            'by_type' => $this->getCountByType(),
            'average_price' => $this->getAveragePrice(),
            'average_area' => $this->getAverageArea(),
            'total_value' => $this->getTotalValue(),
            'sold_last_month' => $this->getSoldLastMonth(),
            'price_range' => $this->getPriceRange(),
            'area_range' => $this->getAreaRange(),
        ];
    }

    private function getCountByStatus(): array
    {
        return Premise::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    private function getCountByType(): array
    {
        return Premise::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
    }

    public function getAveragePrice(): float
    {
        return Premise::avg('base_price') ?? 0;
    }

    private function getAverageArea(): float
    {
        return (float)Premise::avg('total_area') ?? 0;
    }

    public function getTotalValue(): float
    {
        return (float)Premise::sum('base_price') ?? 0;
    }

    public function getSoldLastMonth(): int
    {
        return Premise::where('status', 'sold')
            ->where('updated_at', '>=', now()->subMonth())
            ->count();
    }

    private function getPriceRange(): array
    {
        return [
            'min' => (float)Premise::min('base_price') ?? 0,
            'max' => (float)Premise::max('base_price') ?? 0,
            'avg' => $this->getAveragePrice(),
        ];
    }

    private function getAreaRange(): array
    {
        return [
            'min' => (float)Premise::min('total_area') ?? 0,
            'max' => (float)Premise::max('total_area') ?? 0,
            'avg' => $this->getAverageArea(),
        ];
    }

    public function getFiltered(PremiseFilterDTO $filter): LengthAwarePaginator
    {
        $query = Premise::query()
            ->with([
                'floor.section.building.complex',
                'attachments',
            ]);

        $this->applyFilters($query, $filter);
        $this->applySorting($query, $filter);

        return $query->paginate($filter->perPage);
    }

    private function applyFilters(Builder $query, PremiseFilterDTO $filter): void
    {
        if ($filter->complexId) {
            $query->whereHas('floor.section.building.complex', function ($q) use ($filter) {
                $q->where('id', $filter->complexId);
            });
        }

        if ($filter->buildingId) {
            $query->whereHas('floor.section.building', function ($q) use ($filter) {
                $q->where('id', $filter->buildingId);
            });
        }

        if ($filter->sectionId) {
            $query->whereHas('floor.section', function ($q) use ($filter) {
                $q->where('id', $filter->sectionId);
            });
        }

        if ($filter->floorId) {
            $query->where('floor_id', $filter->floorId);
        }

        if ($filter->status) {
            $query->where('status', $filter->status);
        }

        if ($filter->type) {
            $query->where('type', $filter->type);
        }

        if ($filter->priceFrom !== null) {
            $query->where('base_price', '>=', $filter->priceFrom);
        }

        if ($filter->priceTo !== null) {
            $query->where('base_price', '<=', $filter->priceTo);
        }

        if ($filter->areaFrom !== null) {
            $query->where('total_area', '>=', $filter->areaFrom);
        }

        if ($filter->areaTo !== null) {
            $query->where('total_area', '<=', $filter->areaTo);
        }

        if ($filter->rooms !== null) {
            $query->where('rooms', $filter->rooms);
        }

        if ($filter->features !== null && !empty($filter->features)) {
            foreach ($filter->features as $feature => $value) {
                $query->where("additional_features->{$feature}", $value);
            }
        }
    }

    private function applySorting(Builder $query, PremiseFilterDTO $filter): void
    {
        $sortField = match ($filter->sortBy) {
            'price' => 'base_price',
            'area' => 'total_area',
            'rooms' => 'rooms',
            default => $filter->sortBy,
        };

        if (in_array($sortField, ['base_price', 'total_area', 'rooms', 'created_at', 'updated_at', 'floor'])) {
            $query->orderBy($sortField, $filter->sortOrder);
        }
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Premise::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->paginate($perPage);
    }
}
