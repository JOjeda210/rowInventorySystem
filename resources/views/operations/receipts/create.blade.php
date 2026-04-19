@extends('layouts.app')
@section('title', 'Crear Recepcion')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Nueva Recepcion de Materia Prima</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <form action="{{ route('receipts.store') }}" method="POST" id="receiptForm">
            @csrf

            <div class="card mb-3">
                <div class="card-body p-4">
                    <h6 class="mb-3">Informacion General</h6>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">
                            <i class="bi bi-truck"></i> Proveedor
                        </label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                            <option value="">Selecciona un proveedor...</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="arrived_at" class="form-label">
                            <i class="bi bi-calendar-event"></i> Fecha y Hora de Llegada <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" class="form-control @error('arrived_at') is-invalid @enderror"
                               id="arrived_at" name="arrived_at" value="{{ old('arrived_at', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('arrived_at')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list"></i> Lineas de Recepcion</span>
                        <button type="button" class="btn btn-sm btn-outline-success" id="addLineBtn">
                            <i class="bi bi-plus"></i> Agregar Linea
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="linesContainer">
                        @if(old('lines'))
                            @foreach(old('lines') as $index => $line)
                            <div class="receipt-line p-3 border-bottom" data-index="{{ $index }}">
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label class="form-label">Producto <span class="text-danger">*</span></label>
                                        <select class="form-select product-select" name="lines[{{ $index }}][product_id]" required>
                                            <option value="">Selecciona...</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" @selected(old("lines.$index.product_id") == $product->id)>
                                                {{ $product->name }} ({{ $product->unit->abbreviation }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cant. Recibida <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="lines[{{ $index }}][received_qty]"
                                               value="{{ old("lines.$index.received_qty") }}" step="0.001" min="0.001" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cant. Esperada</label>
                                        <input type="number" class="form-control" name="lines[{{ $index }}][expected_qty]"
                                               value="{{ old("lines.$index.expected_qty") }}" step="0.001" min="0">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm w-100 removeLineBtn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">No. de Lote <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lines[{{ $index }}][lot_number]"
                                               value="{{ old("lines.$index.lot_number") }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha de Vencimiento</label>
                                        <input type="date" class="form-control expiry-date-input" name="lines[{{ $index }}][expiry_date]"
                                               value="{{ old("lines.$index.expiry_date") }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Costo Unitario</label>
                                        <input type="number" class="form-control" name="lines[{{ $index }}][unit_cost]"
                                               value="{{ old("lines.$index.unit_cost") }}" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ubicacion</label>
                                        <select class="form-select" name="lines[{{ $index }}][location_id]">
                                            <option value="">Sin asignar</option>
                                            @foreach($locations as $location)
                                            <option value="{{ $location->id }}" @selected(old("lines.$index.location_id") == $location->id)>
                                                {{ $location->name }} ({{ $location->code }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="text-center py-4 text-muted">
                            <p>No hay lineas. Agrega la primera haciendo clic en el boton.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Crear Recepcion
                </button>
                <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-info-circle"></i> Instrucciones
                </h6>
                <ul class="small">
                    <li>Selecciona el proveedor de la mercancia</li>
                    <li>Agrega una linea por cada producto recibido</li>
                    <li>Especifica el numero de lote si aplica</li>
                    <li>Ingresa la fecha de vencimiento para productos perecederos</li>
                    <li>Revisa los datos antes de confirmar</li>
                </ul>
                <hr>
                <p class="small text-muted mb-0">
                    <i class="bi bi-lightbulb"></i> Se asignara un folio automaticamente
                </p>
            </div>
        </div>
    </div>
</div>

<template id="lineTemplate">
    <div class="receipt-line p-3 border-bottom" data-index="LINE_INDEX">
        <div class="row mb-2">
            <div class="col-md-5">
                <label class="form-label">Producto <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="lines[LINE_INDEX][product_id]" required>
                    <option value="">Selecciona...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }} ({{ $product->unit->abbreviation }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cant. Recibida <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="lines[LINE_INDEX][received_qty]" step="0.001" min="0.001" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cant. Esperada</label>
                <input type="number" class="form-control" name="lines[LINE_INDEX][expected_qty]" step="0.001" min="0">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm w-100 removeLineBtn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">No. de Lote <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="lines[LINE_INDEX][lot_number]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha de Vencimiento</label>
                <input type="date" class="form-control expiry-date-input" name="lines[LINE_INDEX][expiry_date]">
                <div class="expiry-warning text-danger small mt-1" style="display:none">
                    <i class="bi bi-exclamation-triangle"></i> Fecha ya vencida. Confirme si desea continuar.
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Costo Unitario</label>
                <input type="number" class="form-control" name="lines[LINE_INDEX][unit_cost]" step="0.01" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Ubicacion</label>
                <select class="form-select" name="lines[LINE_INDEX][location_id]">
                    <option value="">Sin asignar</option>
                    @foreach($locations as $location)
                    <option value="{{ $location->id }}">
                        {{ $location->name }} ({{ $location->code }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let lineIndex = document.querySelectorAll('.receipt-line').length;

    document.getElementById('addLineBtn').addEventListener('click', function() {
        const template = document.getElementById('lineTemplate');
        const clone = template.content.cloneNode(true);
        const html = clone.innerHTML.replaceAll('LINE_INDEX', lineIndex);
        
        const div = document.createElement('div');
        div.innerHTML = html;
        document.getElementById('linesContainer').appendChild(div.firstElementChild);
        lineIndex++;
    });

    document.getElementById('linesContainer').addEventListener('click', function(e) {
        if (e.target.closest('.removeLineBtn')) {
            e.preventDefault();
            e.target.closest('.receipt-line').remove();
            if (document.querySelectorAll('.receipt-line').length === 0) {
                document.getElementById('linesContainer').innerHTML = '<div class="text-center py-4 text-muted"><p>No hay lineas. Agrega la primera haciendo clic en el boton.</p></div>';
            }
        }
    });
});
</script>
@endsection
