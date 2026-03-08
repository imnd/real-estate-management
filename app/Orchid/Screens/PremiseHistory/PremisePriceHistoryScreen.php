<?php

declare(strict_types=1);

namespace App\Orchid\Screens\PremiseHistory;

use App\Orchid\Layouts\PremiseHistory\PriceListLayout;

class PremisePriceHistoryScreen extends PremiseHistoryScreen
{
    public function query(): iterable
    {
        $query = $this
            ->getBaseQuery()
            ->where('field', 'base_price')
            ->orWhere('field', 'discount_price');

        return [
            'history' => $query->paginate()
        ];
    }

    public function name(): ?string
    {
        return 'История изменений цен';
    }

    public function layout(): iterable
    {
        return [PriceListLayout::class];
    }
}
