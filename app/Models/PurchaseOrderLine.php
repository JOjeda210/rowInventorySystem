<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderLine extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'ordered_qty',
        'unit_cost',
        'origin_alert_id',
    ];

    protected $casts = [
        'ordered_qty' => 'float',
        'unit_cost' => 'float',
    ];

    // Relaciones
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function originAlert(): BelongsTo
    {
        return $this->belongsTo(Alert::class, 'origin_alert_id');
    }
}
