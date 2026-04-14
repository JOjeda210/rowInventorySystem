@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Editar Usuario</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i> Nombre Completo
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Correo Electronico
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required disabled>
                        <small class="text-muted d-block mt-1">El correo no puede ser modificado</small>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">
                            <i class="bi bi-shield-check"></i> Rol
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>
                                Administrador - Acceso completo
                            </option>
                            <option value="warehouse_manager" @selected(old('role', $user->role) === 'warehouse_manager')>
                                Gerente de Almacen - Gestiona operaciones
                            </option>
                            <option value="warehouse_clerk" @selected(old('role', $user->role) === 'warehouse_clerk')>
                                Empleado de Almacen - Ejecuta operaciones
                            </option>
                            <option value="production" @selected(old('role', $user->role) === 'production')>
                                Produccion - Solicita despachos
                            </option>
                            <option value="quality" @selected(old('role', $user->role) === 'quality')>
                                Control de Calidad - Acceso de lectura
                            </option>
                            <option value="purchasing" @selected(old('role', $user->role) === 'purchasing')>
                                Compras - Gestiona ordenes
                            </option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" @checked(old('is_active', $user->is_active))>
                            <label class="form-check-label" for="is_active">
                                <strong>Usuario Activo</strong>
                                <small class="d-block text-muted">
                                    @if($user->is_active)
                                    Este usuario puede iniciar sesion
                                    @else
                                    Este usuario no puede iniciar sesion
                                    @endif
                                </small>
                            </label>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Cambiar Contrasena (Opcional)</h6>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Nueva Contrasena
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            Dejar vacio si no deseas cambiar la contrasena
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock"></i> Confirmar Nueva Contrasena
                        </label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informacion del Usuario
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">ID:</dt>
                    <dd class="col-sm-7"><code>{{ $user->id }}</code></dd>

                    <dt class="col-sm-5">Creado:</dt>
                    <dd class="col-sm-7">
                        <small>{{ $user->created_at->format('d/m/Y H:i') }}</small>
                    </dd>

                    <dt class="col-sm-5">Actualizado:</dt>
                    <dd class="col-sm-7">
                        <small>{{ $user->updated_at->format('d/m/Y H:i') }}</small>
                    </dd>

                    <dt class="col-sm-5">Ultimo Acceso:</dt>
                    <dd class="col-sm-7">
                        <small>
                            @if($user->last_login_at)
                            {{ $user->last_login_at->format('d/m/Y H:i') }}
                            @else
                            Nunca
                            @endif
                        </small>
                    </dd>
                </dl>
            </div>
        </div>

        @if($user->id !== auth()->id())
        <div class="card border-danger mt-3">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle"></i> Peligroso
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">Eliminar este usuario es irreversible</p>
                <form action="{{ route('users.destroy', $user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                            onclick="return confirm('Estas seguro? Esta accion es irreversible')">
                        <i class="bi bi-trash"></i> Eliminar Usuario
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
