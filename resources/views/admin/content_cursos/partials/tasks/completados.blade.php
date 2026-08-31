@extends('adminlte::page')

@section('title', 'Cursos del Estudiante')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
@stop

@section('content_header')
    <h1 class="mb-3 text-center">📘 Estado de Cursos</h1>
@stop

@section('content')

<div class="container-fluid">
    {{-- ✅ CURSOS COMPLETADOS (Aprobados) --}}
    <div class="card card-outline card-success mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-check-circle"></i> Cursos Aprobados</h4>
        </div>
        <div class="card-body">
            @if($cursosCompletados->isEmpty())
                <p class="text-muted">No hay cursos aprobados aún.</p>
            @else
                <div class="row">
                    @foreach ($cursosCompletados as $curso)
                        <div class="col-md-4 mb-4">
                            <div class="card border-success shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-success fw-bold">{{ $curso->nombre ?? $curso->curso_nombre }}</h5>
                                    <p class="mb-0">
                                        <span class="badge badge-success">Aprobado</span>
                                        Curso finalizado cumpliendo tareas, exámenes, fechas y asistencia requeridas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- 🕓 CURSOS EN PROGRESO --}}
    <div class="card card-outline card-warning">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="fas fa-hourglass-half"></i> Cursos en Progreso</h4>
        </div>
        <div class="card-body">
            @if($cursosEnProgreso->isEmpty())
                <p class="text-muted">No hay cursos en progreso actualmente.</p>
            @else
                <div class="row">
                    @foreach ($cursosEnProgreso as $curso)
                        <div class="col-md-4 mb-4">
                            <div class="card border-warning shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-warning fw-bold">{{ $curso->nombre ?? $curso->curso_nombre }}</h5>
                                    <p class="mb-0">
                                        <span class="badge badge-warning text-dark">En progreso</span>
                                        Aún debe completar tareas, exámenes, fechas y/o asistencias del curso.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@stop
