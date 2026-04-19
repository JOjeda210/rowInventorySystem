@extends('layouts.app')
@section('title', 'Reporte de Inventario')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Reporte de Inventario</h1>
    <p class="text-muted">Estado actual del inventario de materia prima</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.inventory') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="category_id" class="form-label">Categoria</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Todas las categorias</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="stock_status" class="form-label">Estado de Stock</label>
                <select name="stock_status" id="stock_status" class="form-select">
                    <option value="all" @selected($stockStatus === 'all')>Todos</option>
                    <option value="low" @selected($stockStatus === 'low')>Stock Bajo</option>
                    <option value="out" @selected($stockStatus === 'out')>Sin Stock</option>
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Productos ({{ $products->count() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered simp-datatable w-100">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Unidad</th>
                    <th>Ubicacion</th>
                    <th>Stock Actual</th>
                    <th>Stock Min.</th>
                    <th>Stock Max.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td><code>{{ $product->code }}</code></td>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>{{ $product->unit?->abbreviation ?? '—' }}</td>
                    <td>{{ $product->location?->name ?? '—' }}</td>
                    <td class="text-end fw-bold">{{ number_format($product->current_stock, 3) }}</td>
                    <td class="text-end">{{ number_format($product->min_stock, 3) }}</td>
                    <td class="text-end">{{ $product->max_stock ? number_format($product->max_stock, 3) : '—' }}</td>
                    <td>
                        @if($product->current_stock <= 0)
                            <span class="badge bg-danger">Sin Stock</span>
                        @elseif($product->min_stock > 0 && $product->current_stock < $product->min_stock)
                            <span class="badge bg-warning text-dark">Stock Bajo</span>
                        @elseif($product->max_stock && $product->current_stock > $product->max_stock)
                            <span class="badge bg-info text-dark">Exceso</span>
                        @else
                            <span class="badge bg-success">OK</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">No hay productos que coincidan con los filtros</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
