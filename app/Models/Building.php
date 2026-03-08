<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCacheInvalidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Building extends Model
{
    use HasCacheInvalidation,
        HasFactory,
        SoftDeletes,
        Filterable,
        AsSource;

    protected array $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'complexes.name' => Like::class,
        'floors_count' => Where::class,
        'year_built' => Where::class,
    ];

    protected array $allowedSorts = [
        'id',
        'name',
        'complexes.name',
        'floors_count',
        'year_built',
    ];

    protected $fillable = [
        'complex_id',
        'name',
        'floors_count',
        'year_built',
    ];

    protected $casts = [
        'floors_count' => 'integer',
        'year_built' => 'integer',
    ];

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function premises(): HasManyThrough
    {
        return $this->hasManyThrough(Premise::class, Floor::class);
    }
}
