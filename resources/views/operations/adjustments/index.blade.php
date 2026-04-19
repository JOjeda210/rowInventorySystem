@extends('layouts.app')
@section('title', 'Ajustes de Inventario')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Ajustes de Inventario</h1>
        <p class="text-muted">Conteos fisicos y correcciones de stock</p>
    </div>
    @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']))
    <a href="{{ route('adjustments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Ajuste
    </a>
    @endif
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-bordered simp-datatable w-100">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Ejecutado por</th>
                    <th>Aprobado por</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adjustments as $adjustment)
                <tr>
                    <td><strong><code>{{ $adjustment->folio }}</code></strong></td>
                    <td>
                        @php
                            $types = ['physical_count' => 'Conteo Fisico', 'waste' => 'Merma', 'return' => 'Devolucion', 'correction' => 'Correccion'];
                        @endphp
                        {{ $types[$adjustment->type] ?? $adjustment->type }}
                    </td>
                    <td>
                        @if($adjustment->status === 'draft')
                            <span class="badge bg-secondary">Borrador</span>
                        @elseif($adjustment->status === 'approved')
                            <span class="badge bg-success">Aprobado</span>
                        @elseif($adjustment->status === 'rejected')
                            <span class="badge bg-danger">Rechazado</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ ucfirst($adjustment->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $adjustment->performedBy?->name ?? '—' }}</td>
                    <td>{{ $adjustment->approvedBy?->name ?? '—' }}</td>
                    <td><small>{{ $adjustment->performed_at?->format('d/m/Y H:i') ?? $adjustment->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>
                        <a href="{{ route('adjustments.show', $adjustment) }}" class="btn btn-sm btn-outline-info" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($adjustment->status === 'draft' && auth()->user()->hasRole(['admin', 'warehouse_manager']) && $adjustment->performed_by !== auth()->id())
                        <form action="{{ route('adjustments.approve', $adjustment) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Aprobar este ajuste? Se actualizara el inventario.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Aprobar">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                        <form action="{{ route('adjustments.reject', $adjustment) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Rechazar este ajuste?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Rechazar">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        No hay ajustes registrados.
                        @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']))
                        <div class="mt-2">
                            <a href="{{ route('adjustments.create') }}" class="btn btn-primary btn-sm">Crear primer ajuste</a>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
