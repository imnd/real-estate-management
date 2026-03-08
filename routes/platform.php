<?php

use Orchid\Platform\Http\Controllers\LoginController;
use App\Orchid\Screens\PremiseHistory\{
    PremisePriceHistoryScreen, PremiseStatusHistoryScreen
};
use App\Orchid\Screens\System\ProfileScreen;
use Tabuna\Breadcrumbs\Trail;

use App\Orchid\Screens\Building\{
    BuildingEditScreen,
    BuildingListScreen
};
use App\Orchid\Screens\Complex\{
    ComplexEditScreen,
    ComplexListScreen
};
use App\Orchid\Screens\DashboardScreen;
use App\Orchid\Screens\Floor\{
    FloorEditScreen,
    FloorListScreen
};
use App\Orchid\Screens\Premise\{
    PremiseEditScreen,
    PremiseListScreen
};
use App\Orchid\Screens\Section\{
    SectionEditScreen,
    SectionListScreen
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Platform Routes
|--------------------------------------------------------------------------
*/

Route::get('login', [LoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/admin/dashboard');
    Route::redirect('/main', '/admin/dashboard')->name('platform.main');

    // Profile
    Route::screen('profile', ProfileScreen::class)
        ->name('platform.profile')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push(__('Профиль')));

    // Dashboard
    Route::screen('dashboard', DashboardScreen::class)
        ->name('platform.dashboard')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push(__('Главная'), route('platform.dashboard')));

    // Complexes
    Route::screen('complexes', ComplexListScreen::class)
        ->name('platform.complex.list')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push('Жилые комплексы'));

    Route::screen('complexes/create', ComplexEditScreen::class)
        ->name('platform.complex.create')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.complex.list')
            ->push('Создание'));

    Route::screen('complexes/{complex}/edit', ComplexEditScreen::class)
        ->name('platform.complex.edit')
        ->breadcrumbs(fn(Trail $trail, $complex) => $trail
            ->parent('platform.complex.list')
            ->push('Редактирование'));

    // Buildings
    Route::screen('buildings', BuildingListScreen::class)
        ->name('platform.building.list')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push('Здания'));

    Route::screen('buildings/create', BuildingEditScreen::class)
        ->name('platform.building.create')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.building.list')
            ->push('Создание'));

    Route::screen('buildings/{building}/edit', BuildingEditScreen::class)
        ->name('platform.building.edit')
        ->breadcrumbs(fn(Trail $trail, $building) => $trail
            ->parent('platform.building.list')
            ->push('Редактирование'));

    // Sections
    Route::screen('sections', SectionListScreen::class)
        ->name('platform.section.list')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push('Секции'));

    Route::screen('sections/create', SectionEditScreen::class)
        ->name('platform.section.create')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.section.list')
            ->push('Создание'));

    Route::screen('sections/{section}/edit', SectionEditScreen::class)
        ->name('platform.section.edit')
        ->breadcrumbs(fn(Trail $trail, $section) => $trail
            ->parent('platform.section.list')
            ->push('Редактирование'));

    // Floors
    Route::screen('floors', FloorListScreen::class)
        ->name('platform.floor.list')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push('Этажи'));

    Route::screen('floors/create', FloorEditScreen::class)
        ->name('platform.floor.create')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.floor.list')
            ->push('Создание'));

    Route::screen('floors/{floor}/edit', FloorEditScreen::class)
        ->name('platform.floor.edit')
        ->breadcrumbs(fn(Trail $trail, $floor) => $trail
            ->parent('platform.floor.list')
            ->push('Редактирование'));

    // Premises
    Route::screen('premises', PremiseListScreen::class)
        ->name('platform.premise.list')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.index')
            ->push('Помещения'));

    Route::screen('premises/create', PremiseEditScreen::class)
        ->name('platform.premise.create')
        ->breadcrumbs(fn(Trail $trail) => $trail
            ->parent('platform.premise.list')
            ->push('Создание'));

    Route::screen('premises/{premise}/edit', PremiseEditScreen::class)
        ->name('platform.premise.edit')
        ->breadcrumbs(fn(Trail $trail, $premise) => $trail
            ->parent('platform.premise.list')
            ->push('Редактирование'));

    Route::screen('premise/history/status', PremiseStatusHistoryScreen::class)
        ->name('platform.premise.history.status');

    Route::screen('premise/history/price', PremisePriceHistoryScreen::class)
        ->name('platform.premise.history.price');

});
