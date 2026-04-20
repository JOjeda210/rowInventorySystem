@extends('layouts.app')
@section('title', 'Reporte de Alertas')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Reporte de Alertas</h1>
    <p class="text-muted">Historial completo de alertas del sistema</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.alerts') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Nivel</label>
                <select name="level" class="form-select">
                    <option value="all" @selected($level === 'all' || !$level)>Todos los niveles</option>
                    <option value="critical" @selected($level === 'critical')>Critico</option>
                    <option value="warning" @selected($level === 'warning')>Advertencia</option>
                    <option value="info" @selected($level === 'info')>Informativo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="is_read" class="form-select">
                    <option value="" @selected(!$isRead)>Todos</option>
                    <option value="unread" @selected($isRead === 'unread')>Sin leer</option>
                    <option value="read" @selected($isRead === 'read')>Leidas</option>
                </select>
            </div>
            <div class="col-md-4">
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
                    <th>Nivel</th>
                    <th>Tipo</th>
                    <th>Producto</th>
                    <th>Mensaje</th>
                    <th>Creada</th>
                    <th>Estado</th>
                    <th>Leida por</th>
                    <th>Leida en</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
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
                    <td>{{ $alert->product?->name ?? '—' }}</td>
                    <td>{{ Str::limit($alert->message, 60) }}</td>
                    <td><small>{{ $alert->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>
                        @if($alert->is_read)
                            <span class="badge bg-success">Leida</span>
                        @else
                            <span class="badge bg-warning text-dark">Sin leer</span>
                        @endif
                    </td>
                    <td>{{ $alert->readBy?->name ?? '—' }}</td>
                    <td>{{ $alert->read_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No hay alertas con los filtros aplicados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($alerts->hasPages())
    <div class="card-footer">{{ $alerts->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
