<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\DispatchStoreRequest;
use App\Models\Dispatch;
use App\Models\DispatchLine;
use App\Models\Lot;
use App\Models\Movement;
use App\Models\Product;
use App\Services\FefoService;
use App\Services\FolioService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DispatchController extends Controller
{
    public function index(): View
    {
        $dispatches = Dispatch::with('requestedBy')
            ->orderBy('requested_at', 'desc')
            ->paginate(20);
        return view('operations.dispatches.index', compact('dispatches'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        return view('operations.dispatches.create', compact('products'));
    }

    public function store(DispatchStoreRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $folio = FolioService::generate('DIS', Dispatch::class);

            $dispatch = Dispatch::create([
                'folio' => $folio,
                'requested_by' => Auth::id(),
                'destination' => $request->destination,
                'requested_at' => Carbon::now(),
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->lines as $lineData) {
                DispatchLine::create([
                    'dispatch_id' => $dispatch->id,
                    'product_id' => $lineData['product_id'],
                    'requested_qty' => $lineData['requested_qty'],
                    'delivered_qty' => 0,
                ]);
            }

            return redirect()->route('dispatches.show', $dispatch)
                ->with('success', 'Despacho creado exitosamente.');
        });
    }

    public function show(Dispatch $dispatch): View
    {
        $dispatch->load('requestedBy', 'fulfilledBy', 'lines.product', 'lines.lot');
        return view('operations.dispatches.show', compact('dispatch'));
    }

    public function fulfill(Dispatch $dispatch): RedirectResponse
    {
        return DB::transaction(function () use ($dispatch) {
            $fefoService = new FefoService();
            $allFulfilled = true;
            $totalDelivered = 0;

            foreach ($dispatch->lines as $line) {
                $product = $line->product;
                $suggestedLots = $fefoService->getSuggestedLots(
                    $line->product_id,
                    $line->requested_qty
                );

                $deliveredQty = 0;

                foreach ($suggestedLots as $suggestedLot) {
                    $lot = $suggestedLot['lot'];
                    $qtyToDeliver = $suggestedLot['qty_to_use'];

                    // Reducir stock del lote
                    $lot->update(['current_qty' => $lot->current_qty - $qtyToDeliver]);

                    // Si el lote se agota, cambiar status
                    if ($lot->current_qty <= 0) {
                        $lot->update(['status' => 'depleted']);
                    }

                    // Crear movimiento
                    Movement::create([
                        'type' => 'dispatch',
                        'lot_id' => $lot->id,
                        'product_id' => $line->product_id,
                        'quantity' => $qtyToDeliver,
                        'qty_before' => $lot->current_qty + $qtyToDeliver,
                        'qty_after' => $lot->current_qty,
                        'reference_id' => $dispatch->id,
                        'reference_type' => 'Dispatch',
                        'user_id' => Auth::id(),
                        'created_at' => Carbon::now(),
                    ]);

                    $deliveredQty += $qtyToDeliver;

                    // Asignar el lote a la linea si es la first
                    if (!$line->lot_id) {
                        $line->update(['lot_id' => $lot->id]);
                    }
                }

                // Actualizar linea
                $line->update(['delivered_qty' => $deliveredQty]);

                if ($deliveredQty < $line->requested_qty) {
                    $allFulfilled = false;
                }

                $totalDelivered += $deliveredQty;
            }

            // Determinar status final
            $status = $allFulfilled ? 'fulfilled' : 'partial';

            $dispatch->update([
                'status' => $status,
                'fulfilled_by' => Auth::id(),
                'delivered_at' => Carbon::now(),
            ]);

            return redirect()->route('dispatches.show', $dispatch)
                ->with('success', 'Despacho surtido exitosamente.');
        });
    }

    public function cancel(Dispatch $dispatch): RedirectResponse
    {
        if ($dispatch->status !== 'pending') {
            return redirect()->route('dispatches.show', $dispatch)
                ->with('error', 'Solo se pueden cancelar despachos en estado pendiente.');
        }

        $dispatch->update(['status' => 'cancelled']);

        return redirect()->route('dispatches.show', $dispatch)
            ->with('success', 'Despacho cancelado exitosamente.');
    }
}
