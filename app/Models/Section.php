<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCacheInvalidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $building_id
 * @property string $name
 * @property int $floor_start
 * @property int $floor_end
 */
class Section extends Model
{
    use HasCacheInvalidation, HasFactory, SoftDeletes, Filterable, AsSource, Attachable;

    protected array $allowedFilters = [
        'id' => Where::class,
        'sections.name' => Like::class,
        'buildings.name' => Like::class,
        'complexes.name' => Like::class,
    ];

    protected array $allowedSorts = [
        'id',
        'sections.name',
        'buildings.name',
        'complexes.name',
    ];

    protected $fillable = [
        'building_id',
        'name',
    ];

    protected $casts = [
        'building_id' => 'integer',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
}
