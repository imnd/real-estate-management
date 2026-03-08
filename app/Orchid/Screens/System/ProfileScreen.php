<?php

declare(strict_types=1);

namespace App\Orchid\Screens\System;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ProfileScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'user' => auth()->user(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Мой профиль';
    }

    /**
     * The description of the screen displayed in the header.
     */
    public function description(): ?string
    {
        return 'Информация о вашем аккаунте';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->method('save')
                ->icon('bs.check-circle'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('user.name')
                    ->title('Имя')
                    ->required()
                    ->placeholder('Введите ваше имя'),

                Input::make('user.email')
                    ->title('Email')
                    ->required()
                    ->placeholder('Введите ваш email'),

                Input::make('password')
                    ->title('Новый пароль')
                    ->type('password')
                    ->placeholder('Оставьте пустым, если не хотите менять'),

                Input::make('password_confirmation')
                    ->title('Подтверждение пароля')
                    ->type('password')
                    ->placeholder('Подтвердите новый пароль'),
            ]),
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function save(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update($request->input('user'));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
        }

        Alert::success('Профиль успешно обновлен');

        return redirect()->route('platform.profile');
    }
}
