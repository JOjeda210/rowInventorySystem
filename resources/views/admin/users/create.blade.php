@extends('layouts.app')
@section('title', 'Crear Usuario')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Crear Nuevo Usuario</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i> Nombre Completo
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Correo Electronico
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Contrasena
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            Minimo 8 caracteres: mayusculas, minusculas, numeros y caracteres especiales
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock"></i> Confirmar Contrasena
                        </label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">
                            <i class="bi bi-shield-check"></i> Rol
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Selecciona un rol...</option>
                            <option value="admin" @selected(old('role') === 'admin')>
                                Administrador - Acceso completo
                            </option>
                            <option value="warehouse_manager" @selected(old('role') === 'warehouse_manager')>
                                Gerente de Almacen - Gestiona operaciones
                            </option>
                            <option value="warehouse_clerk" @selected(old('role') === 'warehouse_clerk')>
                                Empleado de Almacen - Ejecuta operaciones
                            </option>
                            <option value="production" @selected(old('role') === 'production')>
                                Produccion - Solicita despachos
                            </option>
                            <option value="quality" @selected(old('role') === 'quality')>
                                Control de Calidad - Acceso de lectura
                            </option>
                            <option value="purchasing" @selected(old('role') === 'purchasing')>
                                Compras - Gestiona ordenes
                            </option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Crear Usuario
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title mb-3">Roles Disponibles</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><small>Control total del sistema</small></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning text-dark">Gerente</span></td>
                                <td><small>Autoriza operaciones y reportes</small></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info text-dark">Empleado</span></td>
                                <td><small>Realiza recepciones y despachos</small></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary">Produccion</span></td>
                                <td><small>Solicita materiales</small></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary">Calidad</span></td>
                                <td><small>Monitorea inventario (lectura)</small></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">Compras</span></td>
                                <td><small>Gestiona ordenes de compra</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
