<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\UnitOfMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::with('unit')->orderBy('code')->paginate(20);
        return view('catalog.locations.index', compact('locations'));
    }

    public function create(): View
    {
        $units = UnitOfMeasure::orderBy('name')->get();
        return view('catalog.locations.create', compact('units'));
    }

    public function store(): RedirectResponse
    {
        Location::create(request()->validate([
            'code' => ['required', 'string', 'max:20', 'unique:locations,code'],
            'description' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string'],
            'min_temp' => ['nullable', 'numeric'],
            'max_temp' => ['nullable', 'numeric'],
            'max_capacity' => ['nullable', 'numeric'],
            'unit_id' => ['nullable', 'string', 'exists:units_of_measure,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        return redirect()->route('locations.index')
            ->with('success', 'Ubicacion creada exitosamente.');
    }

    public function edit(Location $location): View
    {
        $units = UnitOfMeasure::orderBy('name')->get();
        return view('catalog.locations.edit', compact('location', 'units'));
    }

    public function update(Location $location): RedirectResponse
    {
        $location->update(request()->validate([
            'code' => ['required', 'string', 'max:20', 'unique:locations,code,' . $location->id],
            'description' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string'],
            'min_temp' => ['nullable', 'numeric'],
            'max_temp' => ['nullable', 'numeric'],
            'max_capacity' => ['nullable', 'numeric'],
            'unit_id' => ['nullable', 'string', 'exists:units_of_measure,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        return redirect()->route('locations.index')
            ->with('success', 'Ubicacion actualizada exitosamente.');
    }

    public function toggle(Location $location): RedirectResponse
    {
        $location->update(['is_active' => !$location->is_active]);
        $status = $location->is_active ? 'activada' : 'desactivada';
        return redirect()->route('locations.index')
            ->with('success', "Ubicacion {$status} exitosamente.");
    }
}
