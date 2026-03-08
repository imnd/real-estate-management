<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCacheInvalidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property ?int $section_id
 * @property ?int $building_id
 * @property int $number
 * @property string|null $plan_image
 */
class Floor extends Model
{
    use HasCacheInvalidation, HasFactory, SoftDeletes, Filterable, AsSource, Attachable;

    protected array $allowedFilters = [
        'id' => Where::class,
        'number' => Where::class,
    ];

    protected array $allowedSorts = [
        'id',
        'number',
    ];

    protected $fillable = [
        'section_id',
        'number',
        'plan_image',
    ];

    protected $casts = [
        'section_id' => 'integer',
        'number' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function getPremisesCountAttribute(): int
    {
        return (int)Cache::remember("floor.{$this->id}.premises_count", 3600, fn() => $this->premises()->count());
    }

    public function premises(): HasMany
    {
        return $this->hasMany(Premise::class);
    }

    public function getFullPathAttribute(): string
    {
        $complex = $this->section->building->complex->name ?? 'Неизвестный ЖК';
        $building = $this->section->building->name ?? 'Неизвестное здание';
        $section = $this->section->name ?? 'Неизвестная секция';

        return sprintf('%s / %s / %s / Этаж %d', $complex, $building, $section, $this->number);
    }
}
