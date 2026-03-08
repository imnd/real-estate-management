<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Section;

use App\Models\Building;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SectionEditScreen extends Screen
{
    public ?Section $section = null;

    public function query(Section $section): iterable
    {
        return compact('section');
    }

    public function name(): ?string
    {
        return $this->section->exists ? 'Редактирование секции' : 'Создание секции';
    }

    public function description(): ?string
    {
        return 'Информация о секции';
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
                ->canSee($this->section->exists)
                ->confirm('Вы уверены, что хотите удалить эту секцию?'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('section.building_id')
                    ->title('Здание')
                    ->fromModel(Building::class, 'name')
                    ->required()
                    ->searchable()
                    ->help('Выберите здание, к которому относится секция'),

                Input::make('section.name')
                    ->title('Название секции')
                    ->required()
                    ->placeholder('Например: "Секция А", "Подъезд 1"'),

                Input::make('section.floors_count')
                    ->title('Этажей')
                    ->type('number')
                    ->min(1)
                    ->help('Количество этажей секции'),

            ])->title('Основная информация'),

            Layout::view('platform.sections.floors-preview', ['section' => $this->section]),
        ];
    }

    public function save(Section $section, Request $request): RedirectResponse
    {
        $request->validate([
            'section.building_id' => 'required|exists:buildings,id',
            'section.name' => 'required|string|max:255',
        ]);

        $section->fill($request->get('section'))->save();

        Alert::info('Секция успешно сохранена');

        return redirect()->route('platform.section.list');
    }

    public function remove(Section $section): RedirectResponse
    {
        $section->delete();

        Alert::info('Секция успешно удалена');

        return redirect()->route('platform.section.list');
    }
}
