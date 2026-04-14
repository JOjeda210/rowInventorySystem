<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relaciones
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'received_by');
    }

    public function dispatchesRequested(): HasMany
    {
        return $this->hasMany(Dispatch::class, 'requested_by');
    }

    public function dispatchesFulfilled(): HasMany
    {
        return $this->hasMany(Dispatch::class, 'fulfilled_by');
    }

    public function stockAdjustmentsPerformed(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'performed_by');
    }

    public function stockAdjustmentsApproved(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'approved_by');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'read_by');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    /**
     * Verifica si el usuario tiene uno de los roles especificados
     *
     * @param  string|array  $roles  Rol o array de roles a verificar
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }

    /**
     * Verifica si el usuario es administrador
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verifica si el usuario es personal de almacen (admin, warehouse_manager o warehouse_clerk)
     *
     * @return bool
     */
    public function isWarehouseStaff(): bool
    {
        return in_array($this->role, ['admin', 'warehouse_manager', 'warehouse_clerk']);
    }
}
