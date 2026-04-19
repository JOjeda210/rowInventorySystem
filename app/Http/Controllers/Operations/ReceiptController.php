<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiptStoreRequest;
use App\Models\Location;
use App\Models\Lot;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReceiptLine;
use App\Services\FolioService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function index(): View
    {
        $receipts = Receipt::with('supplier', 'receivedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('operations.receipts.index', compact('receipts'));
    }

    public function create(): View
    {
        $suppliers = DB::table('suppliers')->where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('code')->get();

        return view('operations.receipts.create', compact('suppliers', 'products', 'locations'));
    }

    public function store(ReceiptStoreRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            // Generar folio
            $folio = FolioService::generate('REC', Receipt::class);

            // Crear recepcion
            $receipt = Receipt::create([
                'folio' => $folio,
                'supplier_id' => $request->supplier_id,
                'arrived_at' => $request->arrived_at,
                'received_by' => Auth::id(),
                'status' => 'pending',
            ]);

            // Crear lineas
            $hasDiscrepancies = false;
            foreach ($request->lines as $lineData) {
                $discrepancy = null;
                if ($lineData['expected_qty'] ?? 0) {
                    $discrepancy = $lineData['received_qty'] - $lineData['expected_qty'];
                    if ($discrepancy < 0) {
                        $hasDiscrepancies = true;
                    }
                }

                ReceiptLine::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $lineData['product_id'],
                    'expected_qty' => $lineData['expected_qty'] ?? null,
                    'received_qty' => $lineData['received_qty'],
                    'discrepancy' => $discrepancy,
                    'expiry_date' => $lineData['expiry_date'] ?? null,
                    'lot_number' => $lineData['lot_number'],
                    'unit_cost' => $lineData['unit_cost'] ?? null,
                    'location_id' => $lineData['location_id'] ?? null,
                ]);
            }

            // Actualizar status si hay discrepancias
            if ($hasDiscrepancies) {
                $receipt->update(['status' => 'with_discrepancies']);
            }

            return redirect()->route('receipts.show', $receipt)
                ->with('success', 'Recepcion creada exitosamente.');
        });
    }

    public function show(Receipt $receipt): View
    {
        $receipt->load('supplier', 'receivedBy', 'lines.product', 'lines.location');
        return view('operations.receipts.show', compact('receipt'));
    }

    public function confirm(Receipt $receipt): RedirectResponse
    {
        if (!in_array($receipt->status, ['pending', 'with_discrepancies'])) {
            return redirect()->route('receipts.show', $receipt)
                ->with('error', 'Esta recepcion ya fue confirmada o no puede ser confirmada en su estado actual.');
        }

        return DB::transaction(function () use ($receipt) {
            // Para cada linea de la recepcion
            foreach ($receipt->lines as $line) {
                // Crear lote
                $lot = Lot::create([
                    'lot_number' => $line->lot_number,
                    'product_id' => $line->product_id,
                    'supplier_id' => $receipt->supplier_id,
                    'manufacture_date' => null,
                    'expiry_date' => $line->expiry_date,
                    'received_at' => Carbon::now(),
                    'initial_qty' => $line->received_qty,
                    'current_qty' => $line->received_qty,
                    'location_id' => $line->location_id,
                    'status' => 'available',
                    'notes' => $line->notes ?? null,
                ]);

                // Crear movimiento
                Movement::create([
                    'type' => 'receipt',
                    'lot_id' => $lot->id,
                    'product_id' => $line->product_id,
                    'quantity' => $line->received_qty,
                    'qty_before' => 0,
                    'qty_after' => $line->received_qty,
                    'reference_id' => $receipt->id,
                    'reference_type' => 'Receipt',
                    'user_id' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);

                // Actualizar linea con el lote
                $line->update(['lot_id' => $lot->id]);
            }

            // Actualizar status de la recepcion
            $receipt->update([
                'status' => 'completed',
                'registered_at' => Carbon::now(),
            ]);

            return redirect()->route('receipts.show', $receipt)
                ->with('success', 'Recepcion confirmada exitosamente.');
        });
    }
}
