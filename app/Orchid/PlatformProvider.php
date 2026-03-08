<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    public function registerMainMenu(): array
    {
        return [
            Menu::make('Дашборд')
                ->icon('bs.house')
                ->route('platform.dashboard')
                ->title('Навигация'),

            Menu::make('Жилые комплексы')
                ->icon('bs.building')
                ->route('platform.complex.list'),

            Menu::make('Здания')
                ->icon('bs.grid')
                ->route('platform.building.list'),

            Menu::make('Секции')
                ->icon('bs.layers')
                ->route('platform.section.list'),

            Menu::make('Этажи')
                ->icon('bs.reception-4')
                ->route('platform.floor.list'),

            Menu::make('Помещения')
                ->icon('bs.door-open')
                ->route('platform.premise.list'),

            Menu::make('История изменений помещений')
                ->icon('bs.clock-history')
                ->list([
                    Menu::make('Изменения статусов')
                        ->icon('bs.activity')
                        ->route('platform.premise.history.status'),
                    Menu::make('Изменения цен')
                        ->icon('bs.currency-dollar')
                        ->route('platform.premise.history.price'),
                ])
        ];
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            Menu::make('Профиль')
                ->route('platform.profile')
                ->icon('bs.person'),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }

    public function registerDashboards(): array
    {
        return [
            Dashboard::menu([
                Menu::make('Example Screen')
                    ->icon('bs.book')
                    ->route('platform.example'),
            ]),
        ];
    }
}
