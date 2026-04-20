<?php

namespace App\Observers;

use App\Models\Movement;
use App\Models\Product;
use App\Services\AlertService;
use Illuminate\Support\Facades\DB;

class MovementObserver
{
    /**
     * Handle the Movement "created" event.
     *
     * Se ejecuta cuando se crea un nuevo movimiento. Actualiza el stock del producto
     * segun el tipo de movimiento y genera alertas si corresponde.
     *
     * @param  \App\Models\Movement  $movement
     * @return void
     */
    public function created(Movement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $product = Product::lockForUpdate()->findOrFail($movement->product_id);

            $sumTypes = ['receipt', 'positive_adjustment', 'return'];
            $subtractTypes = ['dispatch', 'negative_adjustment', 'waste'];

            if (in_array($movement->type, $sumTypes)) {
                $product->increment('current_stock', $movement->quantity);
            } elseif (in_array($movement->type, $subtractTypes)) {
                if ($product->current_stock < $movement->quantity) {
                    throw new \InvalidArgumentException(
                        "Stock insuficiente para el producto {$product->name}: disponible {$product->current_stock}, requerido {$movement->quantity}."
                    );
                }
                $product->decrement('current_stock', $movement->quantity);
            }

            $alertService = new AlertService();
            $alertService->checkLowStockAlerts();
        });
    }
}
