@extends('layouts.app')
@section('title', 'Alertas')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Alertas del Sistema</h1>
    <p class="text-muted">Monitoreo de stocks critigos y vencimientos</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeTab" type="button" role="tab">
            <i class="bi bi-exclamation-circle"></i> Alertas Activas
            <span class="badge bg-danger ms-2">{{ $activeAlerts->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="critical-tab" data-bs-toggle="tab" data-bs-target="#criticalTab" type="button" role="tab">
            <i class="bi bi-exclamation-triangle"></i> Criticas
            <span class="badge bg-danger ms-2">{{ $criticalAlerts->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="warning-tab" data-bs-toggle="tab" data-bs-target="#warningTab" type="button" role="tab">
            <i class="bi bi-exclamation-diamond"></i> Advertencias
            <span class="badge bg-warning text-dark ms-2">{{ $warningAlerts->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#allTab" type="button" role="tab">
            <i class="bi bi-list"></i> Todas
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="activeTab" role="tabpanel">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeAlerts as $alert)
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
                            <td>{{ $alert->message }}</td>
                            <td><small>{{ $alert->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                @if(!$alert->is_read)
                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como leida">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted"><i class="bi bi-check-circle"></i> Leida</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #28a745;"></i><br>
                                <strong>No hay alertas activas</strong>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="criticalTab" role="tabpanel">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($criticalAlerts as $alert)
                        <tr>
                            <td>
                                <span class="badge bg-danger">Critico</span>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</td>
                            <td><strong>{{ $alert->product->name }}</strong></td>
                            <td>{{ $alert->message }}</td>
                            <td><small>{{ $alert->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                @if(!$alert->is_read)
                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como leida">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay alertas criticas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="warningTab" role="tabpanel">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warningAlerts as $alert)
                        <tr>
                            <td>
                                <span class="badge bg-warning text-dark">Advertencia</span>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</td>
                            <td><strong>{{ $alert->product->name }}</strong></td>
                            <td>{{ $alert->message }}</td>
                            <td><small>{{ $alert->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                @if(!$alert->is_read)
                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como leida">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay advertencias</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="allTab" role="tabpanel">
        <div class="card">
            <div class="table-responsive">
                <table class="table simp-datatable">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allAlerts as $alert)
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
                            <td>{{ Str::limit($alert->message, 50) }}</td>
                            <td><small>{{ $alert->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                @if($alert->is_read)
                                    <span class="badge bg-success">Leida</span>
                                @else
                                    <span class="badge bg-warning text-dark">Sin leer</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
