<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdjustmentLine extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'adjustment_id',
        'product_id',
        'lot_id',
        'system_qty',
        'physical_qty',
        'variance',
        'error_pct',
        'probable_cause',
    ];

    protected $casts = [
        'system_qty' => 'float',
        'physical_qty' => 'float',
        'variance' => 'float',
        'error_pct' => 'float',
    ];

    // Relaciones
    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class, 'adjustment_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}
