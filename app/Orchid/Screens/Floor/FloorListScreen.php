<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Floor;

use App\Models\Floor;
use App\Orchid\Concerns\HasRelationFilter;
use App\Orchid\Concerns\HasRelationSort;
use App\Orchid\Layouts\FloorListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class FloorListScreen extends Screen
{
    use HasRelationSort, HasRelationFilter;

    public function query(): iterable
    {
        $query = Floor::with('section.building.complex')
            ->withCount('premises')
            ->select('floors.*')
            ->join('sections', 'sections.id', '=', 'floors.section_id')
            ->join('buildings', 'buildings.id', '=', 'sections.building_id')
            ->join('complexes', 'complexes.id', '=', 'buildings.complex_id')
            ->filters();

        $this->applyRelationFilter($query);
        $this->applyRelationSort($query);

        return [
            'floors' => $query->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Этажи';
    }

    public function description(): ?string
    {
        return 'Управление этажами в секциях';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать')
                ->icon('plus')
                ->route('platform.floor.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            FloorListLayout::class,
        ];
    }

    public function remove(Floor $floor)
    {
        $floor->delete();

        Alert::info('Этаж успешно удален');

        return redirect()->route('platform.floor.list');
    }

    protected function getRelationFilters(): array
    {
        return ['sections.name', 'buildings.name', 'complexes.name'];
    }

    protected function getRelationSorts(): array
    {
        return ['sections.name', 'buildings.name', 'complexes.name'];
    }
}
