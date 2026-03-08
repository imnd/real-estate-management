<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Premise;

use App\Models\Premise;
use App\Orchid\Concerns\HasRelationFilter;
use App\Orchid\Concerns\HasRelationSort;
use App\Orchid\Layouts\PremiseListLayout;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class PremiseListScreen extends Screen
{
    use HasRelationSort, HasRelationFilter;

    public function query(): iterable
    {
        /** @var Builder $query */
        $query = Premise::with('floor.section.building.complex')
            ->select('premises.*')
            ->join('floors', 'floors.id', '=', 'premises.floor_id')
            ->join('sections', 'sections.id', '=', 'floors.section_id')
            ->join('buildings', 'buildings.id', '=', 'sections.building_id')
            ->join('complexes', 'complexes.id', '=', 'buildings.complex_id')
            ->filters();

        $this->applyRelationFilter($query);
        $this->applyRelationSort($query);

        return [
            'premises' => $query->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Помещения';
    }

    public function description(): ?string
    {
        return 'Управление помещениями (квартирами)';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать')
                ->icon('plus')
                ->route('platform.premise.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            PremiseListLayout::class,
        ];
    }

    public function remove(Premise $premise)
    {
        $premise->delete();

        Alert::info('Помещение успешно удалено');

        return redirect()->route('platform.premise.list');
    }

    protected function getRelationFilters(): array
    {
        return [
            'premises.status' => 'in',
            'floors.name',
            'floors.number',
            'complexes.name',
            'buildings.name',
            'sections.name',
        ];
    }

    protected function getRelationSorts(): array
    {
        return [
            'floors.name',
            'floors.number',
            'complexes.name',
            'buildings.name',
            'sections.name',
        ];
    }
}
