<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'folio',
        'type',
        'reason',
        'approved_by',
        'performed_by',
        'performed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    // Relaciones
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(AdjustmentLine::class, 'adjustment_id');
    }
}
