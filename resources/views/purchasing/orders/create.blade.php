@extends('layouts.app')
@section('title', 'Nueva Orden de Compra')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Ordenes de Compra</a></li>
        <li class="breadcrumb-item active">Nueva</li>
    </ol>
</nav>

<div class="page-header mb-4">
    <h1 class="page-title">Nueva Orden de Compra</h1>
</div>

<div class="row">
    <div class="col-md-9">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <div class="card mb-3">
                <div class="card-body p-4">
                    <h6 class="mb-3">Informacion General</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
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
                        <div class="col-md-6 mb-3">
                            <label for="required_by" class="form-label">Fecha Requerida</label>
                            <input type="date" class="form-control @error('required_by') is-invalid @enderror"
                                   id="required_by" name="required_by" value="{{ old('required_by') }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                            @error('required_by')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cart"></i> Productos a Ordenar</span>
                    <button type="button" class="btn btn-sm btn-outline-success" id="addLineBtn">
                        <i class="bi bi-plus"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="linesContainer">
                        @error('lines')
                        <div class="alert alert-danger m-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-check-circle"></i> Crear Orden
                </button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<template id="lineTemplate">
    <div class="order-line p-3 border-bottom" data-index="LINE_INDEX">
        <div class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label">Producto <span class="text-danger">*</span></label>
                <select class="form-select" name="lines[LINE_INDEX][product_id]" required>
                    <option value="">Selecciona...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-cost="{{ $product->unit_cost }}">
                        {{ $product->name }} — Stock actual: {{ number_format($product->current_stock, 2) }} {{ $product->unit->abbreviation }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="lines[LINE_INDEX][ordered_qty]"
                       step="0.001" min="0.001" required placeholder="0.000">
            </div>
            <div class="col-md-3">
                <label class="form-label">Costo Unitario</label>
                <input type="number" class="form-control" name="lines[LINE_INDEX][unit_cost]"
                       step="0.01" min="0" placeholder="0.00">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm w-100 removeLineBtn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let lineIndex = 0;

function addLine() {
    const template = document.getElementById('lineTemplate');
    const html = template.innerHTML.replaceAll('LINE_INDEX', lineIndex);
    const div = document.createElement('div');
    div.innerHTML = html;
    document.getElementById('linesContainer').appendChild(div.firstElementChild);
    lineIndex++;
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const lines = document.querySelectorAll('.order-line');
    lines.forEach(l => l.querySelector('.removeLineBtn').disabled = lines.length === 1);
}

document.getElementById('addLineBtn').addEventListener('click', addLine);
document.getElementById('linesContainer').addEventListener('click', e => {
    if (e.target.closest('.removeLineBtn')) {
        e.target.closest('.order-line').remove();
        updateRemoveButtons();
    }
});
document.getElementById('orderForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';
});

addLine();
</script>
@endpush
@endsection
