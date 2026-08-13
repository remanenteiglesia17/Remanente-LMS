@extends('adminlte::page')

@section('title', 'Editar Inscripción')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit mr-2"></i>Editar Inscripción</h1>
        <a href="{{ route('admin.inscripciones.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Cambiar curso del estudiante</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <p class="mb-1"><strong>Estudiante:</strong>
                        {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                    </p>
                    <p class="mb-3"><strong>Curso actual:</strong>
                        <span class="badge badge-secondary">{{ $cursoActual->nombre }}</span>
                    </p>

                    <form method="POST" action="{{ route('admin.inscripciones.update', $inscripcion->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="curso_id">Nuevo curso <span class="text-danger">*</span></label>
                            <select name="curso_id" id="curso_id"
                                    class="form-control @error('curso_id') is-invalid @enderror" required>
                                <option value="">-- Seleccione un curso --</option>
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso->id }}"
                                        {{ old('curso_id', $inscripcion->curso_id) == $curso->id ? 'selected' : '' }}>
                                        {{ $curso->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('curso_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fecha_inscripcion">Fecha de inscripción</label>
                            <input type="date" name="fecha_inscripcion" id="fecha_inscripcion"
                                   class="form-control"
                                   value="{{ old('fecha_inscripcion', substr($inscripcion->fecha_inscripcion ?? '', 0, 10)) }}">
                        </div>

                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-save mr-1"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
