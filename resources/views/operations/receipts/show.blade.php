@extends('layouts.app')
@section('title', 'Recepcion ' . $receipt->folio)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Recepciones</a></li>
        <li class="breadcrumb-item active">{{ $receipt->folio }}</li>
    </ol>
</nav>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ $receipt->folio }}</h1>
        <p class="text-muted">Detalle de recepcion de materia prima</p>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($receipt->status, ['pending', 'with_discrepancies']))
        <form action="{{ route('receipts.confirm', $receipt) }}" method="POST"
              onsubmit="return confirm('¿Confirmar esta recepcion? Se crearan los lotes y movimientos correspondientes. Esta accion es irreversible.')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Confirmar Recepcion
            </button>
        </form>
        @endif
        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
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
                        <th class="text-muted" style="width:40%">Folio</th>
                        <td><code>{{ $receipt->folio }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Estado</th>
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
                    </tr>
                    <tr>
                        <th class="text-muted">Proveedor</th>
                        <td>{{ $receipt->supplier?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Llegada</th>
                        <td>{{ $receipt->arrived_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Registrado</th>
                        <td>{{ $receipt->registered_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Recibido por</th>
                        <td>{{ $receipt->receivedBy?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Creado</th>
                        <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Lineas de Recepcion</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>No. Lote</th>
                            <th>Cant. Esperada</th>
                            <th>Cant. Recibida</th>
                            <th>Discrepancia</th>
                            <th>Vencimiento</th>
                            <th>Ubicacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipt->lines as $line)
                        <tr>
                            <td><strong>{{ $line->product?->name ?? '—' }}</strong></td>
                            <td><code>{{ $line->lot_number }}</code></td>
                            <td>{{ $line->expected_qty !== null ? number_format($line->expected_qty, 3) : '—' }}</td>
                            <td>{{ number_format($line->received_qty, 3) }}</td>
                            <td>
                                @if($line->discrepancy !== null)
                                    @if($line->discrepancy < 0)
                                        <span class="badge bg-danger">{{ number_format($line->discrepancy, 3) }}</span>
                                    @elseif($line->discrepancy > 0)
                                        <span class="badge bg-warning text-dark">+{{ number_format($line->discrepancy, 3) }}</span>
                                    @else
                                        <span class="badge bg-success">0</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($line->expiry_date)
                                    @if($line->expiry_date->isPast())
                                        <span class="text-danger">{{ $line->expiry_date->format('d/m/Y') }} <i class="bi bi-exclamation-triangle"></i></span>
                                    @else
                                        {{ $line->expiry_date->format('d/m/Y') }}
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $line->location?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Sin lineas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
