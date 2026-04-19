@extends('layouts.app')
@section('title', 'Reporte de Movimientos')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Reporte de Movimientos</h1>
    <p class="text-muted">Historial de entradas y salidas del inventario</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.movements') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Producto</label>
                <select name="product_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" @selected($productId == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                    <option value="">Todos</option>
                    <option value="receipt" @selected($type === 'receipt')>Recepcion</option>
                    <option value="dispatch" @selected($type === 'dispatch')>Despacho</option>
                    <option value="positive_adjustment" @selected($type === 'positive_adjustment')>Ajuste +</option>
                    <option value="negative_adjustment" @selected($type === 'negative_adjustment')>Ajuste -</option>
                    <option value="waste" @selected($type === 'waste')>Merma</option>
                    <option value="return" @selected($type === 'return')>Devolucion</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-bordered simp-datatable w-100">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Producto</th>
                    <th>Lote</th>
                    <th>Cantidad</th>
                    <th>Antes</th>
                    <th>Despues</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td><small>{{ $movement->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>
                        @php
                            $typeBadges = ['receipt' => 'bg-success', 'dispatch' => 'bg-danger', 'positive_adjustment' => 'bg-primary', 'negative_adjustment' => 'bg-warning text-dark', 'waste' => 'bg-dark', 'return' => 'bg-info text-dark'];
                            $typeLabels = ['receipt' => 'Recepcion', 'dispatch' => 'Despacho', 'positive_adjustment' => 'Ajuste +', 'negative_adjustment' => 'Ajuste -', 'waste' => 'Merma', 'return' => 'Devolucion'];
                        @endphp
                        <span class="badge {{ $typeBadges[$movement->type] ?? 'bg-secondary' }}">
                            {{ $typeLabels[$movement->type] ?? $movement->type }}
                        </span>
                    </td>
                    <td>{{ $movement->product?->name ?? '—' }}</td>
                    <td>
                        @if($movement->lot)
                            <code>{{ $movement->lot->lot_number }}</code>
                        @else — @endif
                    </td>
                    <td class="text-end fw-bold">{{ number_format($movement->quantity, 3) }}</td>
                    <td class="text-end text-muted">{{ number_format($movement->qty_before, 3) }}</td>
                    <td class="text-end text-muted">{{ number_format($movement->qty_after, 3) }}</td>
                    <td>{{ $movement->user?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No hay movimientos con los filtros aplicados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="card-footer">{{ $movements->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
