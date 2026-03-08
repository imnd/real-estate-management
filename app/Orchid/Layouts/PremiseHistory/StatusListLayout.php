<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\PremiseHistory;

use App\Models\PremiseHistory;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class StatusListLayout extends Table
{
    protected $target = 'history';

    protected function columns(): iterable
    {
        return [
            TD::make('changed_at', 'Дата изменения')
                ->render(fn(PremiseHistory $history) => $history->changed_at->format('d.m.Y H:i:s'))
                ->sort(),

            TD::make('premises.number', 'Помещение')
                ->render(fn(PremiseHistory $history) => $history->premise->full_path),

            TD::make('users.name', 'Пользователь')
                ->filter()
                ->sort()
                ->render(fn(PremiseHistory $history) => $history->user->name),

            TD::make('old_value', 'Старое значение'),

            TD::make('new_value', 'Новое значение'),
        ];
    }
}
