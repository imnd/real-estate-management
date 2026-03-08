<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $premise_id
 * @property int|null $user_id
 * @property string $old_value
 * @property string $new_value
 * @property Carbon $changed_at
 */
class PremiseHistory extends Model
{
    use Filterable, AsSource;

    public $timestamps = false;

    protected $table = 'premise_history';

    protected array $allowedFilters = [
        'id' => Where::class,
        'users.name' => Like::class,
    ];

    protected array $allowedSorts = [
        'id',
        'users.name',
        'changed_at',
    ];

    protected $fillable = [
        'premise_id',
        'user_id',
        'field',
        'old_value',
        'new_value',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function premise(): BelongsTo
    {
        return $this->belongsTo(Premise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
