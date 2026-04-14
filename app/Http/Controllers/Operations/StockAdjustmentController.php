<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustmentStoreRequest;
use App\Models\AdjustmentLine;
use App\Models\Movement;
use App\Models\StockAdjustment;
use App\Services\FolioService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function index(): View
    {
        $adjustments = StockAdjustment::with('performedBy', 'approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('operations.adjustments.index', compact('adjustments'));
    }

    public function create(): View
    {
        return view('operations.adjustments.create');
    }

    public function store(StockAdjustmentStoreRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $folio = FolioService::generate('ADJ', StockAdjustment::class);

            $adjustment = StockAdjustment::create([
                'folio' => $folio,
                'type' => $request->type,
                'reason' => $request->reason,
                'performed_by' => Auth::id(),
                'performed_at' => Carbon::now(),
                'status' => 'draft',
                'notes' => $request->notes,
            ]);

            foreach ($request->lines as $lineData) {
                $lot = DB::table('lots')->find($lineData['lot_id']);
                $systemQty = $lot->current_qty;
                $physicalQty = $lineData['physical_qty'];
                $variance = $physicalQty - $systemQty;
                $errorPct = $systemQty > 0 ? abs($variance / $systemQty) * 100 : 0;

                AdjustmentLine::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $lineData['product_id'],
                    'lot_id' => $lineData['lot_id'],
                    'system_qty' => $systemQty,
                    'physical_qty' => $physicalQty,
                    'variance' => $variance,
                    'error_pct' => $errorPct,
                    'probable_cause' => $lineData['probable_cause'] ?? null,
                ]);
            }

            return redirect()->route('adjustments.show', $adjustment)
                ->with('success', 'Ajuste de inventario creado exitosamente.');
        });
    }

    public function show(StockAdjustment $adjustment): View
    {
        $adjustment->load('performedBy', 'approvedBy', 'lines.product', 'lines.lot');
        return view('operations.adjustments.show', compact('adjustment'));
    }

    public function approve(StockAdjustment $adjustment): RedirectResponse
    {
        if ($adjustment->performed_by === Auth::id()) {
            return redirect()->route('adjustments.show', $adjustment)
                ->with('error', 'El usuario que aprueba debe ser diferente al que ejecuto el ajuste.');
        }

        return DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->lines as $line) {
                $variance = $line->variance;

                // Determinar tipo de movimiento segun varianza
                if ($adjustment->type === 'waste') {
                    $movementType = 'waste';
                } elseif ($variance > 0) {
                    $movementType = 'positive_adjustment';
                } elseif ($variance < 0) {
                    $movementType = 'negative_adjustment';
                } else {
                    continue; // Sin varianza, no crear movimiento
                }

                $lot = $line->lot;

                // Actualizar current_qty del lote
                $lot->update(['current_qty' => $line->physical_qty]);

                // Crear movimiento
                Movement::create([
                    'type' => $movementType,
                    'lot_id' => $lot->id,
                    'product_id' => $line->product_id,
                    'quantity' => abs($variance),
                    'qty_before' => $line->system_qty,
                    'qty_after' => $line->physical_qty,
                    'reference_id' => $adjustment->id,
                    'reference_type' => 'StockAdjustment',
                    'user_id' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            $adjustment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
            ]);

            return redirect()->route('adjustments.show', $adjustment)
                ->with('success', 'Ajuste aprobado exitosamente.');
        });
    }

    public function reject(StockAdjustment $adjustment): RedirectResponse
    {
        $adjustment->update(['status' => 'rejected']);

        return redirect()->route('adjustments.show', $adjustment)
            ->with('success', 'Ajuste rechazado exitosamente.');
    }
}
