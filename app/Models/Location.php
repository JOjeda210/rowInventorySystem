<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'description',
        'type',
        'min_temp',
        'max_temp',
        'max_capacity',
        'unit_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_temp' => 'float',
        'max_temp' => 'float',
        'max_capacity' => 'float',
    ];

    // Relaciones
    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
