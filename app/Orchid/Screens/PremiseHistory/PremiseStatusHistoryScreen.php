<?php

declare(strict_types=1);

namespace App\Orchid\Screens\PremiseHistory;

use App\Orchid\Layouts\PremiseHistory\StatusListLayout;

class PremiseStatusHistoryScreen extends PremiseHistoryScreen
{
    public function query(): iterable
    {
        $query = $this
            ->getBaseQuery()
            ->where('field', 'status');

        return [
            'history' => $query->paginate()
        ];
    }

    public function name(): ?string
    {
        return 'История изменений статусов';
    }

    public function layout(): iterable
    {
        return [StatusListLayout::class];
    }
}
