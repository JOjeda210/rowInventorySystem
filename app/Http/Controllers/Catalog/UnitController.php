<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = UnitOfMeasure::orderBy('name')->paginate(20);
        return view('catalog.units.index', compact('units'));
    }

    public function create(): View
    {
        return view('catalog.units.create');
    }

    public function store(): RedirectResponse
    {
        UnitOfMeasure::create(request()->validate([
            'name' => ['required', 'string', 'max:50', 'unique:units_of_measure,name'],
            'symbol' => ['required', 'string', 'max:10', 'unique:units_of_measure,symbol'],
        ]));
        return redirect()->route('units.index')
            ->with('success', 'Unidad de medida creada exitosamente.');
    }

    public function edit(UnitOfMeasure $unit): View
    {
        return view('catalog.units.edit', compact('unit'));
    }

    public function update(UnitOfMeasure $unit): RedirectResponse
    {
        $unit->update(request()->validate([
            'name' => ['required', 'string', 'max:50', 'unique:units_of_measure,name,' . $unit->id],
            'symbol' => ['required', 'string', 'max:10', 'unique:units_of_measure,symbol,' . $unit->id],
        ]));
        return redirect()->route('units.index')
            ->with('success', 'Unidad de medida actualizada exitosamente.');
    }
}
