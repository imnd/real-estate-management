<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Section;

use App\Models\Section;
use App\Orchid\Concerns\HasRelationFilter;
use App\Orchid\Concerns\HasRelationSort;
use App\Orchid\Layouts\SectionListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class SectionListScreen extends Screen
{
    use HasRelationSort, HasRelationFilter;

    public function query(): iterable
    {
        $query = Section::with('building.complex')
            ->withCount('floors')
            ->select('sections.*')
            ->join('buildings', 'buildings.id', '=', 'sections.building_id')
            ->join('complexes', 'complexes.id', '=', 'buildings.complex_id')
            ->filters();

        $this->applyRelationFilter($query, request()->input('filter'));
        $this->applyRelationSort($query, request()->input('sort'));

        return [
            'sections' => $query->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Секции';
    }

    public function description(): ?string
    {
        return 'Управление секциями в зданиях';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать')
                ->icon('plus')
                ->route('platform.section.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            SectionListLayout::class,
        ];
    }

    public function remove(Section $section)
    {
        $section->delete();

        Alert::info('Секция успешно удалена');

        return redirect()->route('platform.section.list');
    }

    protected function getRelationSorts(): array
    {
        return [
            'sections.name',
            'buildings.name',
            'complexes.name',
        ];
    }

    protected function getRelationFilters(): array
    {
        return [
            'sections.name',
            'buildings.name',
            'complexes.name',
        ];
    }
}
