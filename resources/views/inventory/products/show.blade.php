@extends('layouts.app')
@section('title', $product->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
</nav>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ $product->name }}</h1>
        <p class="text-muted"><code>{{ $product->code }}</code></p>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->hasRole(['admin', 'warehouse_manager']))
        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        @endif
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Informacion del Producto</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted" style="width:45%">Codigo</th><td><code>{{ $product->code }}</code></td></tr>
                    <tr><th class="text-muted">Categoria</th><td>{{ $product->category?->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Unidad</th><td>{{ $product->unit?->abbreviation ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Ubicacion</th><td>{{ $product->location?->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Proveedor preferido</th><td>{{ $product->preferredSupplier?->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">Vida util</th><td>{{ $product->shelf_life_days ? $product->shelf_life_days . ' dias' : '—' }}</td></tr>
                    <tr><th class="text-muted">Costo unitario</th><td>{{ $product->unit_cost ? '$'.number_format($product->unit_cost, 2) : '—' }}</td></tr>
                    <tr><th class="text-muted">Estado</th><td>
                        @if($product->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Stock</div>
            <div class="card-body text-center">
                <div class="display-4 fw-bold
                    @if($product->current_stock <= 0) text-danger
                    @elseif($product->min_stock > 0 && $product->current_stock < $product->min_stock) text-warning
                    @else text-success @endif">
                    {{ number_format($product->current_stock, 3) }}
                </div>
                <div class="text-muted">{{ $product->unit?->abbreviation }}</div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted">Minimo</small><br>
                        <strong>{{ number_format($product->min_stock, 3) }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Maximo</small><br>
                        <strong>{{ $product->max_stock ? number_format($product->max_stock, 3) : '∞' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Lotes Activos ({{ $lots->count() }})</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Numero de Lote</th>
                            <th>Cantidad</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Ubicacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lots as $lot)
                        <tr>
                            <td><code>{{ $lot->lot_number }}</code></td>
                            <td>{{ number_format($lot->current_qty, 3) }}</td>
                            <td>
                                @if($lot->expiry_date)
                                    @if($lot->expiry_date->isPast())
                                        <span class="text-danger">{{ $lot->expiry_date->format('d/m/Y') }} <i class="bi bi-exclamation-triangle"></i></span>
                                    @elseif($lot->expiry_date->diffInDays(now()) <= 7)
                                        <span class="text-warning">{{ $lot->expiry_date->format('d/m/Y') }}</span>
                                    @else
                                        {{ $lot->expiry_date->format('d/m/Y') }}
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php $statusColors = ['available'=>'bg-success','expiring_soon'=>'bg-warning text-dark','critical'=>'bg-danger','expired'=>'bg-dark','depleted'=>'bg-secondary','blocked'=>'bg-secondary']; $statusLabels = ['available'=>'Disponible','expiring_soon'=>'Por vencer','critical'=>'Critico','expired'=>'Vencido','depleted'=>'Agotado','blocked'=>'Bloqueado']; @endphp
                                <span class="badge {{ $statusColors[$lot->status] ?? 'bg-secondary' }}">{{ $statusLabels[$lot->status] ?? $lot->status }}</span>
                            </td>
                            <td>{{ $lot->location?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin lotes activos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Ultimos Movimientos</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td><small>{{ $movement->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                @php $typeBadges = ['receipt'=>'bg-success','dispatch'=>'bg-danger','positive_adjustment'=>'bg-primary','negative_adjustment'=>'bg-warning text-dark','waste'=>'bg-dark','return'=>'bg-info text-dark']; $typeLabels = ['receipt'=>'Recepcion','dispatch'=>'Despacho','positive_adjustment'=>'Ajuste +','negative_adjustment'=>'Ajuste -','waste'=>'Merma','return'=>'Devolucion']; @endphp
                                <span class="badge {{ $typeBadges[$movement->type] ?? 'bg-secondary' }}">{{ $typeLabels[$movement->type] ?? $movement->type }}</span>
                            </td>
                            <td>{{ number_format($movement->quantity, 3) }}</td>
                            <td>{{ $movement->user?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Sin movimientos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
