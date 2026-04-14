<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderStoreRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Supplier;
use App\Services\FolioService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(): View
    {
        $orders = PurchaseOrder::with('supplier', 'createdBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('purchasing.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('purchasing.orders.create', compact('suppliers', 'products'));
    }

    public function store(PurchaseOrderStoreRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $folio = FolioService::generate('PO', PurchaseOrder::class);

            $order = PurchaseOrder::create([
                'folio' => $folio,
                'supplier_id' => $request->supplier_id,
                'created_by' => Auth::id(),
                'status' => 'draft',
                'required_by' => $request->required_by,
                'notes' => $request->notes,
                'created_at' => Carbon::now(),
            ]);

            foreach ($request->lines as $lineData) {
                PurchaseOrderLine::create([
                    'order_id' => $order->id,
                    'product_id' => $lineData['product_id'],
                    'ordered_qty' => $lineData['ordered_qty'],
                    'unit_cost' => $lineData['unit_cost'] ?? null,
                ]);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Orden de compra creada exitosamente.');
        });
    }

    public function show(PurchaseOrder $order): View
    {
        $order->load('supplier', 'createdBy', 'lines.product');
        return view('purchasing.orders.show', compact('order'));
    }

    public function updateStatus(PurchaseOrder $order): RedirectResponse
    {
        $newStatus = request('status');

        $validTransitions = [
            'draft' => ['sent', 'cancelled'],
            'sent' => ['confirmed', 'cancelled'],
            'confirmed' => ['received', 'cancelled'],
            'received' => [],
            'cancelled' => [],
        ];

        if (!isset($validTransitions[$order->status]) || 
            !in_array($newStatus, $validTransitions[$order->status])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Transicion de estado no valida.');
        }

        $order->update(['status' => $newStatus]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Estado de la orden actualizado exitosamente.');
    }
}
