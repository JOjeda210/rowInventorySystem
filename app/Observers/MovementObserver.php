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
            // Recuperar el producto relacionado
            $product = Product::findOrFail($movement->product_id);

            // Determinar si el movimiento suma o resta stock
            $sumTypes = ['receipt', 'positive_adjustment', 'return'];
            $subtractTypes = ['dispatch', 'negative_adjustment', 'waste'];

            if (in_array($movement->type, $sumTypes)) {
                // Sumar al stock
                $product->increment('current_stock', $movement->quantity);
            } elseif (in_array($movement->type, $subtractTypes)) {
                // Restar del stock
                $product->decrement('current_stock', $movement->quantity);
            }

            // Generar alertas si corresponde
            $alertService = new AlertService();
            $alertService->checkLowStockAlerts();
        });
    }
}
