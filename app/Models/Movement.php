<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class Movement extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'type',
        'lot_id',
        'product_id',
        'quantity',
        'qty_before',
        'qty_after',
        'reference_id',
        'reference_type',
        'user_id',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'quantity' => 'float',
        'qty_before' => 'float',
        'qty_after' => 'float',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sobreescribir para evitar actualizaciones
     * Los movimientos son inmutables
     *
     * @return void
     * @throws Throwable
     */
    public function update(array $attributes = [], array $options = [])
    {
        throw new \Exception('Los movimientos son inmutables y no pueden modificarse.');
    }

    /**
     * Sobreescribir para evitar eliminaciones
     * Los movimientos son inmutables
     *
     * @return void
     * @throws Throwable
     */
    public function delete()
    {
        throw new \Exception('Los movimientos son inmutables y no pueden eliminarse.');
    }
}
