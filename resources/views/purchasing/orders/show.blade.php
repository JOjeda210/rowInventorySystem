@extends('layouts.app')
@section('title', 'Orden ' . $order->folio)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Ordenes de Compra</a></li>
        <li class="breadcrumb-item active">{{ $order->folio }}</li>
    </ol>
</nav>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ $order->folio }}</h1>
        <p class="text-muted">Detalle de orden de compra</p>
    </div>
    <div class="d-flex gap-2">
        @php
            $validTransitions = ['draft' => ['sent', 'cancelled'], 'sent' => ['confirmed', 'cancelled'], 'confirmed' => ['received', 'cancelled']];
            $nextStatuses = $validTransitions[$order->status] ?? [];
            $statusLabels = ['sent' => 'Marcar como Enviado', 'confirmed' => 'Marcar Confirmado', 'received' => 'Marcar Recibido', 'cancelled' => 'Cancelar Orden'];
            $statusBtns = ['sent' => 'btn-info', 'confirmed' => 'btn-primary', 'received' => 'btn-success', 'cancelled' => 'btn-outline-danger'];
        @endphp
        @foreach($nextStatuses as $status)
        <form action="{{ route('orders.updateStatus', $order) }}" method="POST"
              onsubmit="return confirm('¿Cambiar estado a: {{ $statusLabels[$status] ?? $status }}?')">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="{{ $status }}">
            <button type="submit" class="btn {{ $statusBtns[$status] ?? 'btn-secondary' }}">
                {{ $statusLabels[$status] ?? $status }}
            </button>
        </form>
        @endforeach
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
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
                    <tr><th class="text-muted" style="width:45%">Folio</th><td><code>{{ $order->folio }}</code></td></tr>
                    <tr><th class="text-muted">Estado</th><td>
                        @php $statusColors = ['draft'=>'bg-secondary','sent'=>'bg-info text-dark','confirmed'=>'bg-primary','received'=>'bg-success','cancelled'=>'bg-dark']; $statusLabels2 = ['draft'=>'Borrador','sent'=>'Enviado','confirmed'=>'Confirmado','received'=>'Recibido','cancelled'=>'Cancelado']; @endphp
                        <span class="badge {{ $statusColors[$order->status] ?? 'bg-secondary' }}">{{ $statusLabels2[$order->status] ?? $order->status }}</span>
                    </td></tr>
                    <tr><th class="text-muted">Proveedor</th><td>{{ $order->supplier?->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Creado por</th><td>{{ $order->createdBy?->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Requerido para</th><td>{{ $order->required_by?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Creado</th><td>{{ $order->created_at->format('d/m/Y H:i') }}</td></tr>
                    @if($order->notes)
                    <tr><th class="text-muted">Notas</th><td>{{ $order->notes }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Productos Ordenados</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Costo Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->lines as $line)
                        <tr>
                            <td><strong>{{ $line->product?->name ?? '—' }}</strong></td>
                            <td>{{ number_format($line->ordered_qty, 3) }}</td>
                            <td>{{ $line->unit_cost ? '$'.number_format($line->unit_cost, 2) : '—' }}</td>
                            <td>{{ $line->unit_cost ? '$'.number_format($line->ordered_qty * $line->unit_cost, 2) : '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Sin productos</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($order->lines->sum(fn($l) => $l->ordered_qty * ($l->unit_cost ?? 0)) > 0)
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="3" class="text-end">Total estimado:</th>
                            <th>${{ number_format($order->lines->sum(fn($l) => $l->ordered_qty * ($l->unit_cost ?? 0)), 2) }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
