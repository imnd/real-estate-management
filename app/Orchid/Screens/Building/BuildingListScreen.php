<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Building;

use App\Models\Building;
use App\Orchid\Concerns\HasRelationFilter;
use App\Orchid\Concerns\HasRelationSort;
use App\Orchid\Layouts\BuildingListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class BuildingListScreen extends Screen
{
    use HasRelationSort, HasRelationFilter;

    public function query(): iterable
    {
        $query = Building::with('complex')
            ->withCount('sections')
            ->select('buildings.*')
            ->join('complexes', 'complexes.id', '=', 'buildings.complex_id')
            ->filters();

        $this->applyRelationFilter($query);
        $this->applyRelationSort($query);

        return [
            'buildings' => $query->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Здания';
    }

    public function description(): ?string
    {
        return 'Управление зданиями в жилых комплексах';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать')
                ->icon('plus')
                ->route('platform.building.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            BuildingListLayout::class,
        ];
    }

    public function remove(Building $building)
    {
        $building->delete();

        Alert::info('Здание успешно удалено');

        return redirect()->route('platform.building.list');
    }

    protected function getRelationFilters(): array
    {
        return [
            'complexes.name',
        ];
    }

    protected function getRelationSorts(): array
    {
        return [
            'complexes.name',
        ];
    }
}
