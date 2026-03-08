<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCacheInvalidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Complex extends Model
{
    use HasCacheInvalidation, HasFactory, SoftDeletes, Filterable, AsSource, Attachable;

    protected array $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'address' => Like::class,
        'status' => Where::class,
    ];

    protected array $allowedSorts = [
        'id',
        'name',
        'address',
        'status',
    ];

    protected $fillable = [
        'name',
        'description',
        'address',
        'status',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }
}
