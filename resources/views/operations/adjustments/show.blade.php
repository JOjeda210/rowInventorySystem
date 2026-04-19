@extends('layouts.app')
@section('title', 'Ajuste ' . $adjustment->folio)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Ajustes</a></li>
        <li class="breadcrumb-item active">{{ $adjustment->folio }}</li>
    </ol>
</nav>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ $adjustment->folio }}</h1>
        <p class="text-muted">Detalle del ajuste de inventario</p>
    </div>
    <div class="d-flex gap-2">
        @if($adjustment->status === 'draft')
            @if(auth()->user()->hasRole(['admin', 'warehouse_manager']) && $adjustment->performed_by !== auth()->id())
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveAdjustmentModal">
                <i class="bi bi-check-circle"></i> Aprobar Ajuste
            </button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectAdjustmentModal">
                <i class="bi bi-x-circle"></i> Rechazar
            </button>
            @elseif($adjustment->performed_by === auth()->id())
            <div class="alert alert-info mb-0 py-2 px-3">
                <i class="bi bi-info-circle"></i> No puedes aprobar tu propio ajuste.
            </div>
            @endif
        @endif
        <a href="{{ route('adjustments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Informacion General</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width:45%">Folio</th>
                        <td><code>{{ $adjustment->folio }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tipo</th>
                        <td>
                            @php $types = ['physical_count' => 'Conteo Fisico', 'waste' => 'Merma', 'return' => 'Devolucion', 'correction' => 'Correccion']; @endphp
                            {{ $types[$adjustment->type] ?? $adjustment->type }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Estado</th>
                        <td>
                            @if($adjustment->status === 'draft')
                                <span class="badge bg-secondary">Borrador</span>
                            @elseif($adjustment->status === 'approved')
                                <span class="badge bg-success">Aprobado</span>
                            @elseif($adjustment->status === 'rejected')
                                <span class="badge bg-danger">Rechazado</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Ejecutado por</th>
                        <td>{{ $adjustment->performedBy?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Aprobado por</th>
                        <td>{{ $adjustment->approvedBy?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Fecha</th>
                        <td>{{ $adjustment->performed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Motivo</th>
                        <td>{{ $adjustment->reason }}</td>
                    </tr>
                    @if($adjustment->notes)
                    <tr>
                        <th class="text-muted">Notas</th>
                        <td>{{ $adjustment->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Lineas del Ajuste</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Cant. Sistema</th>
                            <th>Cant. Fisica</th>
                            <th>Varianza</th>
                            <th>Error %</th>
                            <th>Causa probable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustment->lines as $line)
                        <tr>
                            <td><strong>{{ $line->product?->name ?? '—' }}</strong></td>
                            <td>
                                @if($line->lot)
                                    <code>{{ $line->lot->lot_number }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ number_format($line->system_qty, 3) }}</td>
                            <td>{{ number_format($line->physical_qty, 3) }}</td>
                            <td>
                                @if($line->variance > 0)
                                    <span class="text-success fw-bold">+{{ number_format($line->variance, 3) }}</span>
                                @elseif($line->variance < 0)
                                    <span class="text-danger fw-bold">{{ number_format($line->variance, 3) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>{{ number_format($line->error_pct, 2) }}%</td>
                            <td>{{ $line->probable_cause ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Sin lineas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if($adjustment->status === 'draft' && auth()->user()->hasRole(['admin', 'warehouse_manager']) && $adjustment->performed_by !== auth()->id())
<div class="modal fade" id="approveAdjustmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle text-success"></i> Aprobar Ajuste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Aprobar el ajuste <strong>{{ $adjustment->folio }}</strong>?</p>
                <p class="text-muted mb-0">Se actualizara el inventario con las cantidades fisicas registradas. <strong>Esta accion es irreversible.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('adjustments.approve', $adjustment) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Aprobar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectAdjustmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle text-danger"></i> Rechazar Ajuste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Rechazar el ajuste <strong>{{ $adjustment->folio }}</strong>?</p>
                <p class="text-muted mb-0">El ajuste quedara marcado como rechazado y no se aplicaran cambios al inventario.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <form action="{{ route('adjustments.reject', $adjustment) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Rechazar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
