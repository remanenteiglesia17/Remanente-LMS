@extends('adminlte::page')

@section('title', 'Detalle de Calificaciones - ' . $curso->nombre)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="m-0 text-dark">{{ $curso->nombre }}</h1>
            <p class="text-muted small mb-0">
                Código: {{ $curso->codigo }} | Período: {{ $curso->periodo }}
                @if ($profesor)
                    | Profesor: {{ $profesor->nombres }} {{ $profesor->apellidos }}
                @endif
            </p>
        </div>
        <a href="{{ route('estudiante.calificaciones.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Mis Cursos
        </a>
    </div>
@stop

@php
    $etiquetasTipo = [
        'tarea' => ['label' => 'Tareas', 'icon' => 'fa-clipboard-list', 'color' => 'info'],
        'quiz' => ['label' => 'Quizzes', 'icon' => 'fa-question-circle', 'color' => 'warning'],
        'parcial' => ['label' => 'Parciales', 'icon' => 'fa-file-alt', 'color' => 'primary'],
        'examen' => ['label' => 'Exámenes', 'icon' => 'fa-file-signature', 'color' => 'primary'],
        'proyecto' => ['label' => 'Proyecto', 'icon' => 'fa-project-diagram', 'color' => 'success'],
        'participacion' => ['label' => 'Participación', 'icon' => 'fa-comments', 'color' => 'secondary'],
        'asistencia' => ['label' => 'Asistencia', 'icon' => 'fa-calendar-check', 'color' => 'secondary'],
        'otro' => ['label' => 'Otros', 'icon' => 'fa-ellipsis-h', 'color' => 'secondary'],
    ];
@endphp

@section('content')
    <div class="container-fluid">
        
        {{-- Tarjetas Métricas Superiores --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info shadow-sm">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_evaluaciones'] }}</h3>
                        <p>Evaluaciones Calificadas</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-check"></i></div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box {{ $estadisticas['promedio_ponderado'] >= 3.0 ? 'bg-success' : 'bg-danger' }} shadow-sm">
                    <div class="inner">
                        <h3>{{ number_format($estadisticas['promedio_ponderado'], 2) }}</h3>
                        <p>Promedio Ponderado</p>
                    </div>
                    <div class="icon"><i class="fas fa-trophy"></i></div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success shadow-sm">
                    <div class="inner">
                        <h3>{{ $estadisticas['aprobadas'] }}</h3>
                        <p>Notas Aprobadas (≥ 3.0)</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger shadow-sm">
                    <div class="inner">
                        <h3>{{ $estadisticas['reprobadas'] }}</h3>
                        <p>Notas Reprobadas (&lt; 3.0)</p>
                    </div>
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>

        {{-- Desglose por Módulo y Criterio de Evaluación --}}
        <h4 class="mb-3 font-weight-bold text-secondary">
            <i class="fas fa-layer-group"></i> Resumen por Módulo
        </h4>

        @forelse($porModulo as $m)
            @php $modulo = $m['modulo']; @endphp
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-book"></i> {{ $modulo->nombre }}
                        @if ($modulo->fecha_inicio && $modulo->fecha_fin)
                            <small class="ml-2">({{ $modulo->fecha_inicio->format('d/m/Y') }} - {{ $modulo->fecha_fin->format('d/m/Y') }})</small>
                        @endif
                    </h3>
                    <span class="badge badge-light ml-2">Promedio del módulo: {{ number_format($m['promedio_modulo'], 2) }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($m['por_tipo'] as $tipo => $grupo)
                            @php
                                $meta = $etiquetasTipo[$tipo] ?? ['label' => ucfirst($tipo), 'icon' => 'fa-star', 'color' => 'secondary'];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100 mb-0">
                                    <div class="card-header bg-{{ $meta['color'] }} text-white">
                                        <h3 class="card-title font-weight-bold">
                                            <i class="fas {{ $meta['icon'] }}"></i> {{ $meta['label'] }} (Vale {{ number_format($grupo['peso_categoria'], 1) }}% de este módulo)
                                        </h3>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th class="text-center">Nota</th>
                                                    <th class="text-center">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($grupo['items'] as $calif)
                                                    <tr>
                                                        <td>{{ $calif->concepto }}</td>
                                                        <td class="text-center font-weight-bold text-{{ $calif->color }}">
                                                            {{ number_format($calif->nota, 2) }} / {{ number_format($calif->nota_maxima, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $calif->nota >= 3.0 ? 'badge-success' : 'badge-danger' }}">
                                                                {{ $calif->nota >= 3.0 ? 'Aprobada' : 'Reprobada' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th class="text-right">Promedio:</th>
                                                    <th class="text-center font-weight-bold">{{ number_format($grupo['promedio'], 2) }}</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Todavía no hay calificaciones registradas en este curso.</div>
        @endforelse

        {{-- Tabla Detallada e Histórica --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-list-alt text-primary"></i> Historial Completo de Calificaciones
                </h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Tipo</th>
                            <th class="text-center">Nota / Máx</th>
                            <th>Módulo</th>
                            <th class="text-center">Aporte Final</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calificaciones as $calif)
                            @php 
                                $meta = $etiquetasTipo[$calif->tipo_evaluacion] ?? ['label' => ucfirst($calif->tipo_evaluacion), 'color' => 'secondary'];
                            @endphp
                            <tr>
                                <td>{{ $calif->fecha_calificacion->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $calif->concepto }}</strong>
                                    @if($calif->entrega)
                                        <br>
                                        <a href="{{ route('estudiante.tareas.show', $calif->entrega->tarea_id) }}" 
                                           class="badge badge-primary">
                                            <i class="fas fa-external-link-alt"></i> Ver entrega
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $meta['color'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong class="text-{{ $calif->color }}" style="font-size: 1.1rem;">
                                        {{ number_format($calif->nota, 2) }}
                                    </strong> 
                                    <span class="text-muted">/ {{ $calif->nota_maxima }}</span>
                                </td>
                                <td>{{ $calif->tarea->modulo->nombre ?? '—' }}</td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($calif->aporte_nota_final, 2) }}
                                </td>
                                <td>
                                    @if($calif->observaciones)
                                        <span title="{{ $calif->observaciones }}">
                                            {{ Str::limit($calif->observaciones, 45) }}
                                        </span>
                                    @else
                                        <span class="text-muted italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    No hay calificaciones publicadas para este curso.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@stop