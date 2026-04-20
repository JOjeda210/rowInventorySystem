@extends('layouts.app')
@section('title', 'Ubicaciones')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Ubicaciones de Almacen</h1>
        <p class="text-muted">Gestiona las zonas y ubicaciones del almacen</p>
    </div>
    <a href="{{ route('locations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Ubicacion
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-bordered simp-datatable w-100">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Temp. Min/Max</th>
                    <th>Capacidad</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                <tr>
                    <td><code>{{ $location->code }}</code></td>
                    <td><strong>{{ $location->name }}</strong></td>
                    <td>{{ $location->type ?? '—' }}</td>
                    <td>
                        @if($location->min_temp !== null || $location->max_temp !== null)
                            {{ $location->min_temp ?? '—' }}°C / {{ $location->max_temp ?? '—' }}°C
                        @else —
                        @endif
                    </td>
                    <td>{{ $location->capacity ? number_format($location->capacity, 2) : '—' }}</td>
                    <td>{{ $location->unit?->abbreviation ?? '—' }}</td>
                    <td>
                        @if($location->is_active)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('locations.toggle', $location) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $location->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $location->is_active ? 'Desactivar' : 'Activar' }}">
                                <i class="bi {{ $location->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        No hay ubicaciones registradas.
                        <div class="mt-2">
                            <a href="{{ route('locations.create') }}" class="btn btn-primary btn-sm">Crear primera ubicacion</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locations->hasPages())
    <div class="card-footer">{{ $locations->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
