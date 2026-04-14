<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $categoryId = request('category_id');
        $stockStatus = request('stock_status', 'all');

        $query = Product::with('category', 'unit')->where('is_active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'low') {
            $query->where('current_stock', '>', 0)
                ->whereRaw('current_stock < min_stock');
        } elseif ($stockStatus === 'out') {
            $query->where('current_stock', 0);
        }

        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::orderBy('name')->get();

        return view('inventory.products.index', compact('products', 'categories', 'categoryId', 'stockStatus'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('code')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inventory.products.create', compact('categories', 'units', 'locations', 'suppliers'));
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());
        return redirect()->route('products.show', $product)
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product): View
    {
        $product->load('category', 'unit', 'location', 'preferredSupplier');
        $lots = $product->lots()
            ->where('status', '!=', 'expired')
            ->where('status', '!=', 'depleted')
            ->get();
        $movements = $product->movements()->with('user')->orderBy('created_at', 'desc')->limit(20)->get();
        $alerts = $product->alerts()->orderBy('created_at', 'desc')->get();

        return view('inventory.products.show', compact('product', 'lots', 'movements', 'alerts'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('code')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inventory.products.edit', compact('product', 'categories', 'units', 'locations', 'suppliers'));
    }

    public function update(ProductStoreRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());
        return redirect()->route('products.show', $product)
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);
        $status = $product->is_active ? 'activado' : 'desactivado';
        return redirect()->route('products.index')
            ->with('success', "Producto {$status} exitosamente.");
    }

    public function availableLots(Product $product): JsonResponse
    {
        $lots = $product->lots()
            ->where('status', 'available')
            ->where('current_qty', '>', 0)
            ->orderByRaw('COALESCE(expiry_date, DATE \'2099-12-31\') ASC')
            ->select('id', 'lot_number', 'expiry_date', 'current_qty')
            ->get()
            ->map(function ($lot) {
                return [
                    'id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'expiry_date' => $lot->expiry_date?->format('Y-m-d'),
                    'current_qty' => (float) $lot->current_qty,
                ];
            });

        return response()->json($lots);
    }
}
