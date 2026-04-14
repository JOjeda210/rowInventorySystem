<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptLine extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'receipt_id',
        'product_id',
        'lot_id',
        'expected_qty',
        'received_qty',
        'discrepancy',
        'expiry_date',
        'lot_number',
        'unit_cost',
        'location_id',
        'notes',
    ];

    protected $casts = [
        'expected_qty' => 'float',
        'received_qty' => 'float',
        'discrepancy' => 'float',
        'expiry_date' => 'date',
        'unit_cost' => 'float',
    ];

    // Relaciones
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
