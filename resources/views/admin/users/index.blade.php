@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Usuarios del Sistema</h1>
        <p class="text-muted">Gestiona los usuarios y roles de acceso</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Usuario
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
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Ultimo Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->name }}</strong>
                    </td>
                    <td>
                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                    </td>
                    <td>
                        @php
                        $roleBadges = [
                            'admin' => 'danger',
                            'warehouse_manager' => 'warning',
                            'warehouse_clerk' => 'info',
                            'production' => 'primary',
                            'quality' => 'secondary',
                            'purchasing' => 'success'
                        ];
                        @endphp
                        <span class="badge bg-{{ $roleBadges[$user->role] ?? 'secondary' }}">
                            {{ match($user->role) {
                                'admin' => 'Administrador',
                                'warehouse_manager' => 'Gerente de Almacen',
                                'warehouse_clerk' => 'Empleado de Almacen',
                                'production' => 'Produccion',
                                'quality' => 'Control de Calidad',
                                'purchasing' => 'Compras',
                                default => ucfirst(str_replace('_', ' ', $user->role))
                            } }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        @if($user->last_login_at)
                            <small>{{ $user->last_login_at->format('Y-m-d H:i') }}</small>
                        @else
                            <small class="text-muted">Nunca</small>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar">
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
