<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Enums\CacheTtlType;
use App\Models\Building;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class BuildingFilter extends Filter
{
    public $parameters = ['building_id'];
    protected CacheService $cacheService;

    public function __construct()
    {
        parent::__construct();

        $this->cacheService = app(CacheService::class);
    }

    public function run(Builder $builder): Builder
    {
        $buildingId = $this->request->get('building_id');

        if (empty($buildingId)) {
            return $builder;
        }

        return $builder->whereHas('floor.section', function ($query) use ($buildingId) {
            $query->where('building_id', $buildingId);
        });
    }

    public function display(): array
    {
        $complexId = $this->request->get('complex_id');

        $buildings = $this->cacheService->remember(
            'buildings_list_' . ($complexId ?: 'all'),
            [CacheService::TAG_BUILDINGS],
            function () use ($complexId) {
                return Building::query()
                    ->when($complexId, function ($query) use ($complexId) {
                        $query->where('complex_id', $complexId);
                    })
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray();
            },
            CacheTtlType::Buildings
        );

        return [
            Select::make('building_id')
                ->title('Здание')
                ->options($buildings)
                ->empty('Все здания', '')
                ->placeholder('Выберите здание')
                ->value($this->request->get('building_id')),
        ];
    }

    public function value(): string
    {
        $buildingId = $this->request->get('building_id');

        if (empty($buildingId)) {
            return '';
        }

        $building = Building::find($buildingId);

        return $this->name() . ': ' . ($building->name ?? $buildingId);
    }

    public function name(): string
    {
        return 'Здание';
    }
}
