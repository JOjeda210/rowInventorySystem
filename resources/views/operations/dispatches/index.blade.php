@extends('layouts.app')
@section('title', 'Despachos')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Despachos de Materiales</h1>
        <p class="text-muted">Gestiona las salidas de mercancia</p>
    </div>
    @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk', 'production']))
    <a href="{{ route('dispatches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Despacho
    </a>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table simp-datatable">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Solicitante</th>
                    <th>Fecha</th>
                    <th>Lineas</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dispatches as $dispatch)
                <tr>
                    <td>
                        <strong><code>{{ $dispatch->folio }}</code></strong>
                    </td>
                    <td>
                        {{ $dispatch->requestedBy->name }}
                    </td>
                    <td>
                        <small>{{ $dispatch->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $dispatch->lines->count() }} items</span>
                    </td>
                    <td>
                        @if($dispatch->status === 'pending')
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @elseif($dispatch->status === 'fulfilled')
                            <span class="badge bg-success">Cumplido</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $dispatch->fulfilledBy?->name ?? 'N/A' }}
                    </td>
                    <td>
                        <a href="{{ route('dispatches.show', $dispatch) }}" class="btn btn-sm btn-outline-info" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
