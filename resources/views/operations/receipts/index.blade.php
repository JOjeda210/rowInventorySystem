@extends('layouts.app')
@section('title', 'Recepciones')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Recepciones de Materia Prima</h1>
        <p class="text-muted">Gestiona las entradas de mercancia</p>
    </div>
    @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']))
    <a href="{{ route('receipts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Recepcion
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
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Lineas</th>
                    <th>Total Cantidad</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $receipt)
                <tr>
                    <td>
                        <strong><code>{{ $receipt->folio }}</code></strong>
                    </td>
                    <td>
                        {{ $receipt->supplier->name }}
                    </td>
                    <td>
                        <small>{{ $receipt->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $receipt->lines->count() }} items</span>
                    </td>
                    <td>
                        {{ number_format($receipt->lines->sum('received_qty'), 2) }}
                    </td>
                    <td>
                        @if($receipt->status === 'pending')
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @elseif($receipt->status === 'with_discrepancies')
                            <span class="badge bg-danger">Con discrepancias</span>
                        @elseif($receipt->status === 'completed')
                            <span class="badge bg-success">Completado</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $receipt->status)) }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $receipt->receivedBy?->name ?? 'N/A' }}
                    </td>
                    <td>
                        <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-sm btn-outline-info" title="Ver">
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
