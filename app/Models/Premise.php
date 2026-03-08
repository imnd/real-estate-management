<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCacheInvalidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDate;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $floor_id
 * @property string $number
 * @property string $type
 * @property int $rooms
 * @property float $total_area
 * @property float $living_area
 * @property float $kitchen_area
 * @property string $status
 * @property float $base_price
 * @property float|null $discount_price
 * @property array|null $features
 */
class Premise extends Model
{
    use HasCacheInvalidation, HasFactory, SoftDeletes, Filterable, AsSource, Attachable;

    protected array $allowedFilters = [
        'premises.id' => Where::class,
        'premises.floor_id' => Where::class,
        'premises.number' => Like::class,
        'premises.type' => Where::class,
        'premises.rooms' => Where::class,
        'premises.total_area' => Where::class,
        'premises.living_area' => Where::class,
        'premises.kitchen_area' => Where::class,
        'premises.status' => Where::class,
        'premises.base_price' => Where::class,
        'premises.discount_price' => Where::class,
        'premises.features' => Where::class,
        'premises.plan_image' => Where::class,
        'premises.created_at' => WhereDate::class,
    ];

    protected array $allowedSorts = [
        'premises.id',
        'premises.premises.number',
        'premises.type',
        'premises.rooms',
        'premises.total_area',
        'premises.living_area',
        'premises.kitchen_area',
        'premises.base_price',
        'premises.discount_price',
    ];

    protected $fillable = [
        'floor_id',
        'number',
        'type',
        'rooms',
        'total_area',
        'living_area',
        'kitchen_area',
        'status',
        'base_price',
        'discount_price',
        'features',
        'plan_image',
    ];

    protected $casts = [
        'total_area' => 'decimal:2',
        'living_area' => 'decimal:2',
        'kitchen_area' => 'decimal:2',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'features' => 'array',
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function getPricePerSquareMeterAttribute(string $type): float
    {
        if ($this->total_area <= 0) {
            return 0;
        }

        return round($this->$type / $this->total_area, 2);
    }

    public function getFullPathAttribute(): string
    {
        return sprintf('%s / Номер %d', $this->floor->full_path, $this->number);
    }
}
