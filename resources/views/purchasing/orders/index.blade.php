@extends('layouts.app')
@section('title', 'Ordenes de Compra')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Ordenes de Compra</h1>
        <p class="text-muted">Gestion de ordenes a proveedores</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Orden
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-bordered simp-datatable w-100">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Proveedor</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Requerido para</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><strong><code>{{ $order->folio }}</code></strong></td>
                    <td>{{ $order->supplier?->name ?? '—' }}</td>
                    <td>
                        @php
                            $statusColors = ['draft' => 'bg-secondary', 'sent' => 'bg-info text-dark', 'confirmed' => 'bg-primary', 'received' => 'bg-success', 'cancelled' => 'bg-dark'];
                            $statusLabels = ['draft' => 'Borrador', 'sent' => 'Enviado', 'confirmed' => 'Confirmado', 'received' => 'Recibido', 'cancelled' => 'Cancelado'];
                        @endphp
                        <span class="badge {{ $statusColors[$order->status] ?? 'bg-secondary' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td>{{ $order->createdBy?->name ?? '—' }}</td>
                    <td>{{ $order->required_by?->format('d/m/Y') ?? '—' }}</td>
                    <td><small>{{ $order->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-info" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        No hay ordenes de compra registradas.
                        <div class="mt-2">
                            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">Crear primera orden</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
