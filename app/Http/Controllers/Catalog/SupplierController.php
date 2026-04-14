<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->paginate(20);
        return view('catalog.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('catalog.suppliers.create');
    }

    public function store(): RedirectResponse
    {
        Supplier::create(request()->validate([
            'code' => ['required', 'string', 'max:20', 'unique:suppliers,code'],
            'name' => ['required', 'string', 'max:150'],
            'contact' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:120'],
            'tax_id' => ['nullable', 'string', 'max:15'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('catalog.suppliers.edit', compact('supplier'));
    }

    public function update(Supplier $supplier): RedirectResponse
    {
        $supplier->update(request()->validate([
            'code' => ['required', 'string', 'max:20', 'unique:suppliers,code,' . $supplier->id],
            'name' => ['required', 'string', 'max:150'],
            'contact' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:120'],
            'tax_id' => ['nullable', 'string', 'max:15'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function toggle(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        $status = $supplier->is_active ? 'activado' : 'desactivado';
        return redirect()->route('suppliers.index')
            ->with('success', "Proveedor {$status} exitosamente.");
    }
}
