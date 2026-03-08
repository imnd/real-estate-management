<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Floor;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Section;
use App\Services\CacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class FloorEditScreen extends Screen
{
    public ?Floor $floor = null;

    protected CacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(CacheService::class);
    }

    public function query(Floor $floor, Request $request): iterable
    {
        if (!$floor->exists && $request->has('section_id')) {
            $floor->section_id = $request->input('section_id');
            $floor->building_id = $request->input('building_id');
        }

        return compact('floor');
    }

    public function name(): ?string
    {
        return $this->floor->exists ? 'Редактирование этажа' : 'Создание этажа';
    }

    public function description(): ?string
    {
        return 'Информация об этаже';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->floor->exists)
                ->confirm('Вы уверены, что хотите удалить этот этаж?'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('floor.building_id')
                    ->title('Здание')
                    ->fromModel(Building::class, 'name')
                    ->searchable()
                    ->help('Укажите здание напрямую, если этаж не привязан к секции'),

                Relation::make('floor.section_id')
                    ->title('Секция')
                    ->fromModel(Section::class, 'name')
                    ->searchable()
                    ->help('Если выбрана секция, здание будет определено через неё'),

                Input::make('floor.number')
                    ->title('Номер этажа')
                    ->type('number')
                    ->required()
                    ->min(1)
                    ->max(200)
                    ->help('Номер этажа в здании'),

                Upload::make('floor.attachment')
                    ->title('План этажа')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1),

            ])->title('Основная информация'),

            Layout::view('platform.floors.premises-preview', ['floor' => $this->floor]),
        ];
    }

    public function save(Floor $floor, Request $request): RedirectResponse
    {
        $request->validate([
            'floor.section_id' => 'nullable|exists:sections,id',
            'floor.building_id' => 'nullable|exists:buildings,id',
            'floor.number' => 'required|integer|min:1|max:200',
            'floor.area' => 'nullable|numeric|min:0',
        ]);

        $data = $request->get('floor');

        if (!empty($data['section_id'])) {
            $data['building_id'] = Section::find($data['section_id'])->building_id;
        }

        $floor->fill($data)->save();

        $this->cacheService->flush([
            CacheService::TAG_FLOORS,
            CacheService::TAG_COMPLEXES,
            CacheService::TAG_BUILDINGS,
            CacheService::TAG_STATISTICS,
        ]);

        $attachmentIds = $request->input('floor.attachment', []);

        if (!empty($attachmentIds)) {
            $floor->attachment()->sync($attachmentIds);
            $attachment = $floor->attachment()->first();
            if ($attachment) {
                $floor->plan_image = $attachment->id;
                $floor->save();
            }
        } else {
            $floor->attachment()->detach();
            $floor->plan_image = null;
            $floor->save();
        }

        Alert::info('Этаж успешно сохранен');

        return redirect()->route('platform.floor.list');
    }

    public function remove(Floor $floor): RedirectResponse
    {
        $floor->delete();

        Alert::info('Этаж успешно удален');

        return redirect()->route('platform.floor.list');
    }
}
