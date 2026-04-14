<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Lot;
use App\Models\Product;
use Carbon\Carbon;

class AlertService
{
    /**
     * Revisa lotes proximos a vencer y genera alertas de expiracion
     *
     * Busca lotes con status available o expiring_soon donde la fecha de caducidad
     * este entre hoy y hoy + 7 dias. Determina el tipo y nivel de alerta segun
     * dias restantes y evita duplicados de alertas activas.
     *
     * @return void
     */
    public function checkExpiryAlerts(): void
    {
        $today = Carbon::now()->startOfDay();
        $sevenDaysFromNow = Carbon::now()->addDays(7)->startOfDay();

        // Buscar lotes proximos a vencer
        $lotsToCheck = Lot::where(function ($query) {
            $query->where('status', 'available')
                  ->orWhere('status', 'expiring_soon');
        })
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $sevenDaysFromNow])
            ->get();

        foreach ($lotsToCheck as $lot) {
            $daysUntilExpiry = $today->diffInDays($lot->expiry_date, false);
            $daysUntilExpiry = max($daysUntilExpiry, 0);

            // Determinar tipo y nivel de alerta
            if ($daysUntilExpiry <= 1) {
                $alertType = 'expiry_1d';
                $level = 'critical';
                $alertLevel = 'remove_1d';
            } elseif ($daysUntilExpiry <= 3) {
                $alertType = 'expiry_3d';
                $level = 'warning';
                $alertLevel = 'urgent_3d';
            } else {
                $alertType = 'expiry_7d';
                $level = 'info';
                $alertLevel = 'warn_7d';
            }

            // Verificar que no exista ya una alerta activa del mismo tipo para este lote
            $existingAlert = Alert::where('lot_id', $lot->id)
                ->where('type', $alertType)
                ->where('is_read', false)
                ->exists();

            if (!$existingAlert) {
                Alert::create([
                    'type' => $alertType,
                    'product_id' => $lot->product_id,
                    'lot_id' => $lot->id,
                    'message' => "Lote {$lot->lot_number} del producto vence en {$daysUntilExpiry} dias.",
                    'level' => $level,
                    'is_read' => false,
                ]);
            }

            // Actualizar el alert_level del lote
            $lot->update(['alert_level' => $alertLevel]);
        }
    }

    /**
     * Revisa productos con stock bajo y genera alertas correspondientes
     *
     * Busca productos activos donde current_stock < min_stock y min_stock > 0.
     * Crea alertas low_stock con level warning si el stock es bajo pero mayor a 0,
     * y alertas out_of_stock con level critical si el stock es 0.
     *
     * @return void
     */
    public function checkLowStockAlerts(): void
    {
        // Buscar productos con stock bajo
        $productsLowStock = Product::where('is_active', true)
            ->where('min_stock', '>', 0)
            ->whereRaw('current_stock < min_stock AND current_stock > 0')
            ->get();

        foreach ($productsLowStock as $product) {
            // Verificar que no exista ya una alerta low_stock activa
            $existingAlert = Alert::where('product_id', $product->id)
                ->where('type', 'low_stock')
                ->where('is_read', false)
                ->exists();

            if (!$existingAlert) {
                Alert::create([
                    'type' => 'low_stock',
                    'product_id' => $product->id,
                    'message' => "El producto {$product->name} tiene stock bajo: {$product->current_stock} unidades (minimo: {$product->min_stock}).",
                    'level' => 'warning',
                    'is_read' => false,
                ]);
            }
        }

        // Buscar productos sin stock
        $productsOutOfStock = Product::where('is_active', true)
            ->where('current_stock', 0)
            ->where('min_stock', '>', 0)
            ->get();

        foreach ($productsOutOfStock as $product) {
            // Verificar que no exista ya una alerta out_of_stock activa
            $existingAlert = Alert::where('product_id', $product->id)
                ->where('type', 'out_of_stock')
                ->where('is_read', false)
                ->exists();

            if (!$existingAlert) {
                Alert::create([
                    'type' => 'out_of_stock',
                    'product_id' => $product->id,
                    'message' => "El producto {$product->name} no tiene stock disponible.",
                    'level' => 'critical',
                    'is_read' => false,
                ]);
            }
        }
    }
}
