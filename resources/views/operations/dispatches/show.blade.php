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
        <form action="{{ route('dispatches.fulfill', $dispatch) }}" method="POST"
              onsubmit="return confirm('¿Surtir este despacho? Se actualizara el inventario y generaran movimientos. Esta accion es irreversible.')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="bi bi-box-arrow-up"></i> Surtir Despacho
            </button>
        </form>
        @endif
        @if(in_array($dispatch->status, ['pending']) && auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk', 'production']))
        <form action="{{ route('dispatches.cancel', $dispatch) }}" method="POST"
              onsubmit="return confirm('¿Cancelar este despacho?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-x-circle"></i> Cancelar
            </button>
        </form>
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
@endsection
