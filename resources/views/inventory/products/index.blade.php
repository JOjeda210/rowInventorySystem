@extends('layouts.app')
@section('title', 'Productos')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Productos del Inventario</h1>
        <p class="text-muted">Gestiona el catalogo de productos</p>
    </div>
    @if(auth()->user()->hasRole(['admin', 'warehouse_manager']))
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Producto
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
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Stock Actual</th>
                    <th>Stock Min/Max</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <code>{{ $product->id }}</code>
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $product->current_stock }}</strong> {{ $product->unit->abbreviation }}
                    </td>
                    <td>
                        <small>
                            {{ $product->minimum_stock }} / {{ $product->maximum_stock }}
                        </small>
                    </td>
                    <td>
                        @if($product->current_stock > $product->maximum_stock)
                            <span class="badge bg-warning text-dark">Exceso</span>
                        @elseif($product->current_stock < $product->minimum_stock)
                            <span class="badge bg-danger">Bajo</span>
                        @elseif($product->current_stock > 0)
                            <span class="badge bg-success">OK</span>
                        @else
                            <span class="badge bg-secondary">Agotado</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->hasRole(['admin', 'warehouse_manager']))
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
