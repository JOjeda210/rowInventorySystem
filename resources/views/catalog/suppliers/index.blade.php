@extends('layouts.app')
@section('title', 'Proveedores')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Proveedores</h1>
    </div>
    @if(auth()->user()->hasRole(['admin', 'purchasing']))
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Proveedor
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
                    <th>Contacto</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>RFC</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                <tr>
                    <td><code>{{ $supplier->code }}</code></td>
                    <td>
                        <strong>{{ $supplier->name }}</strong>
                    </td>
                    <td>
                        {{ $supplier->contact ?? '—' }}
                    </td>
                    <td>
                        @if($supplier->email)
                        <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($supplier->phone)
                        <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                        @else —
                        @endif
                    </td>
                    <td>{{ $supplier->tax_id ?? '—' }}</td>
                    <td>
                        @if($supplier->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->hasRole(['admin', 'purchasing']))
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary" title="Editar">
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
