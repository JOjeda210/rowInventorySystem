@extends('layouts.app')
@section('title', 'Despacho ' . $dispatch->folio)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('dispatches.index') }}">Despachos</a></li>
        <li class="breadcrumb-item active">{{ $dispatch->folio }}</li>
    </ol>
</nav>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ $dispatch->folio }}</h1>
        <p class="text-muted">Detalle del despacho de materiales</p>
    </div>
    <div class="d-flex gap-2">
        @if($dispatch->status === 'pending' && auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']))
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#fulfillDispatchModal">
            <i class="bi bi-box-arrow-up"></i> Surtir Despacho
        </button>
        @endif
        @if($dispatch->status === 'pending' && auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk', 'production']))
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelDispatchModal">
            <i class="bi bi-x-circle"></i> Cancelar
        </button>
        @endif
        <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary">
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
                        <td><code>{{ $dispatch->folio }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Estado</th>
                        <td>
                            @if($dispatch->status === 'pending')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($dispatch->status === 'fulfilled')
                                <span class="badge bg-success">Surtido</span>
                            @elseif($dispatch->status === 'partial')
                                <span class="badge bg-warning text-dark">Parcial</span>
                            @elseif($dispatch->status === 'cancelled')
                                <span class="badge bg-secondary">Cancelado</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($dispatch->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Solicitante</th>
                        <td>{{ $dispatch->requestedBy?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Destino</th>
                        <td>{{ $dispatch->destination ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Solicitado</th>
                        <td>{{ $dispatch->requested_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Entregado</th>
                        <td>{{ $dispatch->delivered_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Surtido por</th>
                        <td>{{ $dispatch->fulfilledBy?->name ?? '—' }}</td>
                    </tr>
                    @if($dispatch->notes)
                    <tr>
                        <th class="text-muted">Notas</th>
                        <td>{{ $dispatch->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Lineas del Despacho</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Solicitado</th>
                            <th>Entregado</th>
                            <th>Lote</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispatch->lines as $line)
                        <tr>
                            <td><strong>{{ $line->product?->name ?? '—' }}</strong></td>
                            <td>{{ number_format($line->requested_qty, 3) }}</td>
                            <td>{{ number_format($line->delivered_qty ?? 0, 3) }}</td>
                            <td>
                                @if($line->lot)
                                    <code>{{ $line->lot->lot_number }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($line->delivered_qty >= $line->requested_qty)
                                    <span class="badge bg-success">Completo</span>
                                @elseif(($line->delivered_qty ?? 0) > 0)
                                    <span class="badge bg-warning text-dark">Parcial</span>
                                @else
                                    <span class="badge bg-secondary">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Sin lineas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if($dispatch->status === 'pending')
<div class="modal fade" id="fulfillDispatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-arrow-up text-success"></i> Surtir Despacho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Confirmar el surtido del despacho <strong>{{ $dispatch->folio }}</strong>?</p>
                <p class="text-muted mb-0">Se descontara el inventario por lote (FEFO) y se generaran los movimientos. <strong>Esta accion es irreversible.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('dispatches.fulfill', $dispatch) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success"><i class="bi bi-box-arrow-up"></i> Surtir</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cancelDispatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle text-danger"></i> Cancelar Despacho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Cancelar el despacho <strong>{{ $dispatch->folio }}</strong>?</p>
                <p class="text-muted mb-0">El despacho quedara marcado como cancelado y no podra reactivarse.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <form action="{{ route('dispatches.cancel', $dispatch) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar Despacho</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
