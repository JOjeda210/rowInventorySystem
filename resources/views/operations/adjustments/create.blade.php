@extends('layouts.app')
@section('title', 'Nuevo Ajuste de Inventario')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Ajustes</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol>
</nav>

<div class="page-header mb-4">
    <h1 class="page-title">Nuevo Ajuste de Inventario</h1>
    <p class="text-muted">Registra un conteo fisico o correccion de stock</p>
</div>

<div class="row">
    <div class="col-md-9">
        <form action="{{ route('adjustments.store') }}" method="POST" id="adjustmentForm">
            @csrf

            <div class="card mb-3">
                <div class="card-body p-4">
                    <h6 class="mb-3">Informacion General</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Tipo de Ajuste <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Selecciona...</option>
                                <option value="physical_count" @selected(old('type') == 'physical_count')>Conteo Fisico</option>
                                <option value="waste" @selected(old('type') == 'waste')>Merma / Desperdicio</option>
                                <option value="return" @selected(old('type') == 'return')>Devolucion</option>
                                <option value="correction" @selected(old('type') == 'correction')>Correccion</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="reason" class="form-label">Motivo <span class="text-danger">*</span> <small class="text-muted">(min. 20 caracteres)</small></label>
                            <input type="text" class="form-control @error('reason') is-invalid @enderror"
                                   id="reason" name="reason" value="{{ old('reason') }}" required minlength="20">
                            @error('reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas adicionales</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    @if(auth()->id() === auth()->id())
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle"></i>
                        <strong>Nota:</strong> El ajuste debe ser aprobado por un usuario diferente al que lo ejecuta.
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check"></i> Productos a Ajustar</span>
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
                    <i class="bi bi-check-circle"></i> Guardar Ajuste
                </button>
                <a href="{{ route('adjustments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="col-md-3">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Instrucciones</h6>
                <ul class="small">
                    <li>Selecciona el producto y lote a ajustar</li>
                    <li>La cantidad en sistema se carga automaticamente</li>
                    <li>Ingresa la cantidad fisica contada</li>
                    <li>El ajuste requiere aprobacion de otro usuario</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<template id="lineTemplate">
    <div class="adjustment-line p-3 border-bottom" data-index="LINE_INDEX">
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="form-label">Producto <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="lines[LINE_INDEX][product_id]" required data-line="LINE_INDEX">
                    <option value="">Selecciona...</option>
                    @foreach(App\Models\Product::where('is_active', true)->orderBy('name')->with('unit')->get() as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Lote <span class="text-danger">*</span></label>
                <select class="form-select lot-select" name="lines[LINE_INDEX][lot_id]" required data-line="LINE_INDEX" disabled>
                    <option value="">Primero selecciona producto</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cant. Sistema</label>
                <input type="number" class="form-control system-qty-input" readonly
                       name="lines[LINE_INDEX][system_qty_display]" placeholder="—" step="0.001">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cant. Fisica <span class="text-danger">*</span></label>
                <input type="number" class="form-control physical-qty-input" name="lines[LINE_INDEX][physical_qty]"
                       step="0.001" min="0" required data-line="LINE_INDEX" placeholder="0.000">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm w-100 removeLineBtn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label">Varianza</label>
                <div class="variance-display fw-bold py-2 text-muted">—</div>
            </div>
            <div class="col-md-5">
                <label class="form-label">Causa Probable</label>
                <input type="text" class="form-control" name="lines[LINE_INDEX][probable_cause]"
                       placeholder="Opcional">
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
    const lineEl = div.firstElementChild;
    document.getElementById('linesContainer').appendChild(lineEl);

    const productSelect = lineEl.querySelector('.product-select');
    const lotSelect = lineEl.querySelector('.lot-select');
    const systemQtyInput = lineEl.querySelector('.system-qty-input');
    const physicalQtyInput = lineEl.querySelector('.physical-qty-input');
    const varianceDisplay = lineEl.querySelector('.variance-display');

    productSelect.addEventListener('change', function() {
        const productId = this.value;
        if (!productId) {
            lotSelect.innerHTML = '<option value="">Primero selecciona producto</option>';
            lotSelect.disabled = true;
            return;
        }
        lotSelect.innerHTML = '<option value="">Cargando...</option>';
        lotSelect.disabled = true;
        fetch(`/api/products/${productId}/lots`)
            .then(r => r.json())
            .then(lots => {
                if (lots.length === 0) {
                    lotSelect.innerHTML = '<option value="">Sin lotes disponibles</option>';
                } else {
                    lotSelect.innerHTML = '<option value="">Selecciona un lote...</option>' +
                        lots.map(l => `<option value="${l.id}" data-qty="${l.current_qty}">
                            ${l.lot_number} — ${l.current_qty} u. ${l.expiry_date ? '(Vence: ' + l.expiry_date + ')' : ''}
                        </option>`).join('');
                    lotSelect.disabled = false;
                }
            });
    });

    lotSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const qty = selected ? parseFloat(selected.dataset.qty) || 0 : 0;
        systemQtyInput.value = qty > 0 ? qty.toFixed(3) : '';
        updateVariance();
    });

    physicalQtyInput.addEventListener('input', updateVariance);

    function updateVariance() {
        const sysQty = parseFloat(systemQtyInput.value) || 0;
        const phyQty = parseFloat(physicalQtyInput.value) || 0;
        if (!systemQtyInput.value) { varianceDisplay.textContent = '—'; varianceDisplay.className = 'variance-display fw-bold py-2 text-muted'; return; }
        const variance = phyQty - sysQty;
        varianceDisplay.textContent = (variance >= 0 ? '+' : '') + variance.toFixed(3);
        varianceDisplay.className = 'variance-display fw-bold py-2 ' + (variance > 0 ? 'text-success' : variance < 0 ? 'text-danger' : 'text-muted');
    }

    lineIndex++;
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const lines = document.querySelectorAll('.adjustment-line');
    lines.forEach(line => {
        line.querySelector('.removeLineBtn').disabled = lines.length === 1;
    });
}

document.getElementById('addLineBtn').addEventListener('click', addLine);

document.getElementById('linesContainer').addEventListener('click', function(e) {
    if (e.target.closest('.removeLineBtn')) {
        e.target.closest('.adjustment-line').remove();
        updateRemoveButtons();
    }
});

document.getElementById('adjustmentForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});

addLine();
</script>
@endpush
@endsection
