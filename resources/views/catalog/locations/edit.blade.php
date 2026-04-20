@extends('layouts.app')
@section('title', 'Editar Ubicacion')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Ubicaciones</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
</nav>

<div class="page-header mb-4">
    <h1 class="page-title">Editar: {{ $location->name }}</h1>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('locations.update', $location) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $location->name) }}" required maxlength="100">
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Codigo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $location->code) }}" required maxlength="20">
                            @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripcion</label>
                        <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $location->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Tipo</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Sin clasificar</option>
                                <option value="refrigerated" @selected(old('type', $location->type) == 'refrigerated')>Refrigerado</option>
                                <option value="dry" @selected(old('type', $location->type) == 'dry')>Seco</option>
                                <option value="frozen" @selected(old('type', $location->type) == 'frozen')>Congelado</option>
                                <option value="ambient" @selected(old('type', $location->type) == 'ambient')>Ambiente</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="min_temp" class="form-label">Temp. Min (°C)</label>
                            <input type="number" class="form-control" id="min_temp" name="min_temp"
                                   value="{{ old('min_temp', $location->min_temp) }}" step="0.1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="max_temp" class="form-label">Temp. Max (°C)</label>
                            <input type="number" class="form-control" id="max_temp" name="max_temp"
                                   value="{{ old('max_temp', $location->max_temp) }}" step="0.1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="capacity" class="form-label">Capacidad</label>
                            <input type="number" class="form-control" id="capacity" name="capacity"
                                   value="{{ old('capacity', $location->capacity) }}" step="0.001" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="unit_id" class="form-label">Unidad</label>
                            <select class="form-select" id="unit_id" name="unit_id">
                                <option value="">—</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected(old('unit_id', $location->unit_id) == $unit->id)>
                                    {{ $unit->name }} ({{ $unit->abbreviation }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active', $location->is_active))>
                            <label class="form-check-label" for="is_active">Ubicacion activa</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
