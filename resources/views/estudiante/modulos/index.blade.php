{{-- resources/views/estudiante/modulos/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Módulos - ' . $curso->nombre)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $curso->nombre }}</h1>
            <small class="text-muted">
                Módulos del curso
                @if ($profesor)
                    &middot; Dictado por {{ $profesor->nombres }} {{ $profesor->apellidos }}
                @endif
            </small>
        </div>
        <div>
            <span class="badge badge-primary">Estudiante</span>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if ($modulos->isEmpty())
            <div class="alert alert-info">
                Tu profesor todavía no ha creado módulos para este curso.
            </div>
        @else
            <div class="row">
                @foreach ($modulos as $modulo)
                    <div class="col-md-4 mb-4">
                        <div class="card {{ $modulo->desbloqueado ? 'card-outline card-success' : 'card-outline card-secondary' }}">
                            <div class="card-header">
                                <h3 class="card-title">
                                    @if (!$modulo->desbloqueado)
                                        <i class="fas fa-lock text-secondary mr-1"></i>
                                    @elseif ($modulo->finalizado)
                                        <i class="fas fa-check-circle text-success mr-1"></i>
                                    @else
                                        <i class="fas fa-unlock text-success mr-1"></i>
                                    @endif
                                    Módulo {{ $modulo->orden }}: {{ $modulo->nombre }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @if ($modulo->descripcion)
                                    <p>{{ $modulo->descripcion }}</p>
                                @endif
                                <p class="text-muted mb-0">{{ $modulo->tareas_count }} tarea(s)</p>
                            </div>
                            <div class="card-footer">
                                @if ($modulo->desbloqueado)
                                    <a href="{{ route('estudiante.tareas.index', ['modulo_id' => $modulo->id]) }}"
                                        class="btn btn-primary btn-block">
                                        Ver tareas <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-block" disabled>
                                        Bloqueado — termina el módulo anterior primero
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@stop
