<?php

namespace App\Services;

use App\Models\Lot;
use Illuminate\Support\Collection;

class FefoService
{
    /**
     * Obtiene los lotes sugeridos para surtir una cantidad requerida usando logica FEFO
     *
     * First-Expired-First-Out: ordena los lotes por fecha de caducidad (los proximos a vencer primero)
     * y calcula cuantos lotes se necesitan para cubrir la cantidad requerida.
     *
     * @param  string  $productId  ID del producto
     * @param  float  $requiredQty  Cantidad requerida a surtir
     * @return \Illuminate\Support\Collection
     */
    public function getSuggestedLots(string $productId, float $requiredQty): Collection
    {
        // Buscar lotes disponibles del producto ordenados por fecha de caducidad
        // null al final para lotes sin fecha de vencimiento
        $lots = Lot::where('product_id', $productId)
            ->where('status', 'available')
            ->where('current_qty', '>', 0)
            ->orderByRaw('COALESCE(expiry_date, DATE \'2099-12-31\') ASC')
            ->get();

        $suggested = collect();
        $remainingQty = $requiredQty;

        // Iterar sobre los lotes hasta cubrir la cantidad requerida o agotar los lotes
        foreach ($lots as $lot) {
            if ($remainingQty <= 0) {
                break;
            }

            // Determinar cuanta cantidad tomar de este lote
            $qtyFromThisLot = min($lot->current_qty, $remainingQty);

            // Agregar el lote a la coleccion con su cantidad sugerida
            $suggested->push([
                'lot' => $lot,
                'qty_to_use' => $qtyFromThisLot,
            ]);

            $remainingQty -= $qtyFromThisLot;
        }

        return $suggested;
    }
}
