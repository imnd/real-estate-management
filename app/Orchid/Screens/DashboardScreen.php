<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Enums\CacheTtlType;
use App\Enums\PremiseHistoryField;
use App\Enums\PremiseStatus;
use App\Models\Premise;
use App\Models\PremiseHistory;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Layouts\Chart;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class DashboardScreen extends Screen
{
    protected CacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(CacheService::class);
    }

    public function query(): iterable
    {
        $metrics = $this->cacheService->remember(
            'dashboard_metrics',
            [CacheService::TAG_STATISTICS],
            function () {
                return [
                    'Доступно' => [
                        'value' => Premise::where('status', PremiseStatus::Available)->count(),
                        'color' => 'text-success'
                    ],
                    'Бронь' => [
                        'value' => Premise::where('status', PremiseStatus::Reserved)->count(),
                        'color' => 'text-warning'
                    ],
                    'Продано' => [
                        'value' => Premise::where('status', PremiseStatus::Sold)->count(),
                        'color' => 'text-danger'
                    ],
                ];
            },
            CacheTtlType::Dashboard
        );

        $pieData = $this->cacheService->remember(
            'dashboard_pie',
            [CacheService::TAG_STATISTICS],
            function () {
                return [
                    [
                        'labels' => ['Доступно', 'Забронировано', 'Продано'],
                        'values' => [
                            Premise::where('status', PremiseStatus::Available)->count(),
                            Premise::where('status', PremiseStatus::Reserved)->count(),
                            Premise::where('status', PremiseStatus::Sold)->count(),
                        ],
                    ]
                ];
            },
            CacheTtlType::Dashboard
        );

        $salesData = $this->cacheService->remember(
            'dashboard_sales',
            [CacheService::TAG_STATISTICS],
            function () {
                $salesTrend = PremiseHistory::where('field', PremiseHistoryField::Status->value)
                    ->where('new_value', PremiseStatus::Sold->value)
                    ->where('changed_at', '>=', now()->subDays(30))
                    ->select(DB::raw('DATE(changed_at) as date'), DB::raw('count(*) as count'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                return [
                    [
                        'name' => 'Продано объектов',
                        'values' => $salesTrend->pluck('count')->toArray(),
                        'labels' => $salesTrend->pluck('date')->toArray(),
                    ]
                ];
            },
            CacheTtlType::Dashboard
        );

        $topExpensive = $this->cacheService->remember(
            'dashboard_top_expensive',
            [CacheService::TAG_STATISTICS, CacheService::TAG_PREMISES],
            function () {
                return Premise::orderByDesc('base_price')->limit(10)->get();
            },
            CacheTtlType::Dashboard
        );

        $recentHistory = $this->cacheService->remember(
            'dashboard_recent_history',
            [CacheService::TAG_STATISTICS, CacheService::TAG_PREMISES],
            function () {
                return PremiseHistory::with(['premise', 'user'])
                    ->where('field', PremiseHistoryField::Status->value)
                    ->latest('changed_at')
                    ->limit(6)
                    ->get();
            },
            CacheTtlType::Dashboard
        );

        return [
            'metrics' => $metrics,
            'premise_pie' => $pieData,
            'sales_performance' => $salesData,
            'top_expensive' => $topExpensive,
            'recent_history' => $recentHistory,
        ];
    }

    public function name(): ?string
    {
        return 'Панель управления';
    }

    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Доступно' => 'metrics.Доступно',
                'Бронь' => 'metrics.Бронь',
                'Продано' => 'metrics.Продано',
            ]),

            Layout::columns([
                new class extends Chart {
                    protected $target = 'premise_pie';
                    protected $title = 'Статусы помещений';
                    protected $type = self::TYPE_PIE;
                    protected $height = 300;

                    protected function colors(): array
                    {
                        return ['#28a745', '#ffc107', '#dc3545'];
                    }
                },

                Layout::chart('sales_performance')
                    ->title('График продаж (30 дней)')
                    ->type(Chart::TYPE_LINE)
                    ->height(300),
            ]),

            Layout::columns([
                Layout::table('top_expensive', [
                    TD::make('number', '№'),
                    TD::make('total_area', 'Площадь')->render(fn($p) => $p->total_area . ' м²'),
                    TD::make('base_price', 'Цена')->render(
                        fn($p) => number_format((float)$p->base_price, 0, '.', ' ') . ' ₽'
                    )->alignRight(),
                ])->title('Топ-10 дорогих объектов'),

                Layout::table('recent_history', [
                    TD::make('premise_id', 'Объект')->render(fn($h) => '№' . $h->premise->number),
                    TD::make('new_value', 'Статус')->render(fn($h) => view('status_badge', ['status' => $h->new_value])
                    ),
                    TD::make('changed_at', 'Время')->render(fn($h) => $h->changed_at->diffForHumans()),
                ])->title('Последние изменения'),
            ]),
        ];
    }
}
