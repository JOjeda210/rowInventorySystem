@extends('layouts.app')
@section('title', 'Editar Unidad de Medida')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('units.index') }}">Unidades</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
</nav>

<div class="page-header mb-4">
    <h1 class="page-title">Editar: {{ $unit->name }}</h1>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('units.update', $unit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $unit->name) }}" required maxlength="50">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="abbreviation" class="form-label">Abreviacion <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('abbreviation') is-invalid @enderror"
                               id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required maxlength="10">
                        @error('abbreviation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
