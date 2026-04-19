<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitOfMeasure extends Model
{
    use HasUuids;

    protected $table = 'units_of_measure';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    // Relaciones
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'unit_id');
    }
}
