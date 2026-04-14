@extends('layouts.app')
@section('title', 'Panel de Control')

@section('content')
<div class="page-header">
    <h1 class="page-title">Panel de Control</h1>
</div>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Productos Activos</p>
                        <h3 class="mb-0">{{ $activeProductsCount }}</h3>
                    </div>
                    <i class="bi bi-box-seam" style="font-size: 2rem; color: #0d6efd;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Alertas Criticas</p>
                        <h3 class="mb-0">{{ $criticalAlertsCount }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: #dc3545;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Stock Bajo</p>
                        <h3 class="mb-0">{{ $lowStockCount }}</h3>
                    </div>
                    <i class="bi bi-arrow-down-circle" style="font-size: 2rem; color: #ffc107;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Recepciones Hoy</p>
                        <h3 class="mb-0">{{ $receiptsToday }}</h3>
                    </div>
                    <i class="bi bi-truck" style="font-size: 2rem; color: #198754;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts & Quick Access -->
<div class="row mb-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bell"></i> Ultimas Alertas
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAlerts as $alert)
                        <tr>
                            <td>
                                @if($alert->level === 'critical')
                                    <span class="badge bg-danger">Critico</span>
                                @elseif($alert->level === 'warning')
                                    <span class="badge bg-warning text-dark">Advertencia</span>
                                @else
                                    <span class="badge bg-info text-dark">Info</span>
                                @endif
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</td>
                            <td><strong>{{ $alert->product->name }}</strong></td>
                            <td><small>{{ Str::limit($alert->message, 40) }}</small></td>
                            <td><small>{{ $alert->created_at->diffForHumans() }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No hay alertas recientes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('alerts.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right"></i> Ver todas
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning"></i> Accesos Rapidos
            </div>
            <div class="list-group list-group-flush">
                @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']))
                <a href="{{ route('receipts.create') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nueva Recepcion</span>
                </a>
                <a href="{{ route('receipts.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-list"></i>
                    <span>Listar Recepciones</span>
                </a>
                @endif

                @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk', 'production']))
                <a href="{{ route('dispatches.create') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nuevo Despacho</span>
                </a>
                <a href="{{ route('dispatches.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-list"></i>
                    <span>Listar Despachos</span>
                </a>
                @endif

                @if(auth()->user()->hasRole(['admin', 'warehouse_manager']))
                <a href="{{ route('products.create') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nuevo Producto</span>
                </a>
                <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-list"></i>
                    <span>Listar Productos</span>
                </a>
                @endif

                <a href="{{ route('alerts.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-bell"></i>
                    <span>Ver Alertas</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Movements -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-arrow-left-right"></i> Ultimos Movimientos
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMovements as $movement)
                <tr>
                    <td>
                        @if($movement->type === 'receipt')
                            <span class="badge bg-success">Entrada</span>
                        @elseif($movement->type === 'dispatch')
                            <span class="badge bg-danger">Salida</span>
                        @elseif(in_array($movement->type, ['waste', 'negative_adjustment']))
                            <span class="badge bg-danger">Descuento</span>
                        @else
                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $movement->type)) }}</span>
                        @endif
                    </td>
                    <td><strong>{{ $movement->product->name }}</strong></td>
                    <td>
                        <span class="{{ $movement->quantity >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                        </span>
                    </td>
                    <td>{{ $movement->user->name }}</td>
                    <td><small>{{ $movement->created_at->format('Y-m-d H:i') }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">No hay movimientos recientes</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
