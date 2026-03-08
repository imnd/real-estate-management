<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\PremiseHistoryField;
use App\Models\Premise;
use App\Models\PremiseHistory;
use Illuminate\Support\Facades\Auth;

class PremiseObserver
{
    public function updated(Premise $premise): void
    {
        $userId = Auth::id();
        foreach (PremiseHistoryField::getList() as $field) {
            if ($premise->isDirty($field)) {
                PremiseHistory::create([
                    'premise_id' => $premise->id,
                    'user_id' => $userId,
                    'field' => $field,
                    'old_value' => $premise->getOriginal($field),
                    'new_value' => $premise->$field,
                ]);
            }
        }
    }
}
