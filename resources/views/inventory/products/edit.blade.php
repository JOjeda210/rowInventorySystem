@extends('layouts.app')
@section('title', 'Editar Producto')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
</nav>

<div class="page-header mb-4">
    <h1 class="page-title">Editar Producto</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Codigo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $product->code) }}" required maxlength="20">
                            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripcion</label>
                        <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Selecciona...</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_id" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                <option value="">Selecciona...</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id) == $unit->id)>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                @endforeach
                            </select>
                            @error('unit_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location_id" class="form-label">Ubicacion</label>
                            <select class="form-select" id="location_id" name="location_id">
                                <option value="">Sin asignar</option>
                                @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id', $product->location_id) == $location->id)>{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="preferred_supplier_id" class="form-label">Proveedor Preferido</label>
                            <select class="form-select" id="preferred_supplier_id" name="preferred_supplier_id">
                                <option value="">Ninguno</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('preferred_supplier_id', $product->preferred_supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Configuracion de Stock</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="min_stock" class="form-label">Stock Minimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                                   id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" step="0.001" min="0" required>
                            @error('min_stock')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="max_stock" class="form-label">Stock Maximo</label>
                            <input type="number" class="form-control @error('max_stock') is-invalid @enderror"
                                   id="max_stock" name="max_stock" value="{{ old('max_stock', $product->max_stock) }}" step="0.001" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shelf_life_days" class="form-label">Vida util (dias)</label>
                            <input type="number" class="form-control" id="shelf_life_days" name="shelf_life_days"
                                   value="{{ old('shelf_life_days', $product->shelf_life_days) }}" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="unit_cost" class="form-label">Costo Unitario</label>
                        <input type="number" class="form-control" id="unit_cost" name="unit_cost"
                               value="{{ old('unit_cost', $product->unit_cost) }}" step="0.01" min="0">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
