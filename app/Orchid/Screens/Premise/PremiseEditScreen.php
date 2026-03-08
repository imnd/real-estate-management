<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Premise;

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use App\Models\Floor;
use App\Models\Premise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PremiseEditScreen extends Screen
{
    public $premise;

    public function query(Premise $premise, Request $request): iterable
    {
        if (!$premise->exists && $request->has('floor_id')) {
            $premise->floor_id = $request->input('floor_id');
        }

        return compact('premise');
    }

    public function name(): ?string
    {
        return $this->premise->exists ? 'Редактирование помещения' : 'Создание помещения';
    }

    public function description(): ?string
    {
        return 'Информация о помещении (квартире)';
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
                ->canSee($this->premise->exists)
                ->confirm('Вы уверены, что хотите удалить это помещение?'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('premise.floor_id')
                    ->title('Этаж')
                    ->fromModel(Floor::class, 'number')
                    ->required()
                    ->searchable()
                    ->help('Выберите этаж, на котором находится помещение')
                    ->displayAppend('full_path'),

                Input::make('premise.number')
                    ->title('Номер помещения')
                    ->required()
                    ->placeholder('Например: 123, 45А')
                    ->help('Уникальный номер помещения на этаже'),

                Select::make('premise.type')
                    ->title('Тип')
                    ->options(PremiseType::mappedTypes())
                    ->required()
                    ->help('Тип помещения'),

                Input::make('premise.rooms')
                    ->title('Количество комнат')
                    ->type('number')
                    ->required()
                    ->min(1)
                    ->max(20)
                    ->value(1),

                Input::make('premise.total_area')
                    ->title('Общая площадь')
                    ->type('number')
                    ->required()
                    ->step('0.1')
                    ->min(1)
                    ->help('Общая площадь в квадратных метрах'),

                Input::make('premise.living_area')
                    ->title('Жилая площадь')
                    ->type('number')
                    ->required()
                    ->step('0.1')
                    ->min(1)
                    ->help('Жилая площадь в квадратных метрах'),

                Input::make('premise.kitchen_area')
                    ->title('Площадь кухни')
                    ->type('number')
                    ->required()
                    ->step('0.1')
                    ->min(1)
                    ->help('Площадь кухни в квадратных метрах'),

                Input::make('premise.base_price')
                    ->title('Цена')
                    ->type('number')
                    ->required()
                    ->step('1000')
                    ->min(0)
                    ->max(100000000)
                    ->help('Цена'),

                Input::make('premise.discount_price')
                    ->title('Цена со скидкой')
                    ->type('number')
                    ->step('1000')
                    ->min(0)
                    ->max(100000000)
                    ->help('Цена со скидкой'),

                Select::make('premise.status')
                    ->title('Статус')
                    ->options(PremiseStatus::mappedStatuses())
                    ->required()
                    ->help('Текущий статус помещения'),

                TextArea::make('premise.description')
                    ->title('Описание')
                    ->rows(5)
                    ->placeholder('Введите описание помещения (планировка, особенности и т.д.)'),

                Matrix::make('premise.features')
                    ->title('Особенности (Характеристики)')
                    ->columns([
                        'Характеристика' => 'name',
                        'Значение' => 'value',
                    ])
                    ->help('Пример: "Балкон" — "Застеклен", "Вид" — "Во двор"'),

                Upload::make('premise.attachment')
                    ->title('Галерея')
                    ->acceptedFiles('image/*')
                    ->maxFiles(10),

            ])->title('Информация о помещении'),

            Layout::view('platform.premises.location-info', ['premise' => $this->premise]),
        ];
    }

    public function save(Premise $premise, Request $request): RedirectResponse
    {
        $request->validate([
            'premise.floor_id' => 'required|exists:floors,id',
            'premise.number' => 'required|string|max:50',
            'premise.rooms' => 'required|integer|min:1|max:20',
            'premise.total_area' => 'required|numeric|min:0',
            'premise.living_area' => 'required|numeric|min:0',
            'premise.kitchen_area' => 'required|numeric|min:0',
            'premise.base_price' => 'nullable|numeric|min:0',
            'premise.discount_price' => 'nullable|numeric|min:0',
            'premise.status' => 'required|in:' . implode(',', PremiseStatus::getList()),
        ]);

        $premise->fill($request->get('premise'))->save();

        $premise->attachment()->sync($request->input('premise.attachment', []));

        Alert::info('Помещение успешно сохранено');

        return redirect()->route('platform.premise.list');
    }

    public function remove(Premise $premise): RedirectResponse
    {
        $premise->delete();

        Alert::info('Помещение успешно удалено');

        return redirect()->route('platform.premise.list');
    }
}
