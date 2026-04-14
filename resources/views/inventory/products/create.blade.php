@extends('layouts.app')
@section('title', 'Crear Producto')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Crear Nuevo Producto</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-box-seam"></i> Nombre del Producto
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            <i class="bi bi-tag"></i> Categoria
                        </label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Selecciona una categoria...</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_id" class="form-label">
                            <i class="bi bi-rulers"></i> Unidad de Medida
                        </label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                            <option value="">Selecciona una unidad...</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>
                                {{ $unit->name }} ({{ $unit->abbreviation }})
                            </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="location_id" class="form-label">
                            <i class="bi bi-geo-alt"></i> Ubicacion de Almacenamiento
                        </label>
                        <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                            <option value="">Selecciona una ubicacion...</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                {{ $location->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('location_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="preferred_supplier_id" class="form-label">
                            <i class="bi bi-truck"></i> Proveedor Preferido
                        </label>
                        <select class="form-select @error('preferred_supplier_id') is-invalid @enderror" id="preferred_supplier_id" name="preferred_supplier_id">
                            <option value="">Ninguno</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('preferred_supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('preferred_supplier_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h6 class="mb-3">Configuracion de Stock</h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_stock" class="form-label">Stock Minimo</label>
                                <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" step="0.01" required>
                                @error('minimum_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="maximum_stock" class="form-label">Stock Maximo</label>
                                <input type="number" class="form-control @error('maximum_stock') is-invalid @enderror" 
                                       id="maximum_stock" name="maximum_stock" value="{{ old('maximum_stock', 1000) }}" step="0.01" required>
                                @error('maximum_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_stock" class="form-label">Stock Inicial</label>
                                <input type="number" class="form-control @error('current_stock') is-invalid @enderror" 
                                       id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" step="0.01" required>
                                @error('current_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Crear Producto
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title mb-2">
                    <i class="bi bi-info-circle"></i> Consejos
                </h6>
                <ul class="small">
                    <li>Los productos deben tener nombres descriptivos</li>
                    <li>La categoria ayuda a organizar el inventario</li>
                    <li>Establece limites reales de stock minimo y maximo</li>
                    <li>El proveedor preferido es opcional pero recomendado</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
