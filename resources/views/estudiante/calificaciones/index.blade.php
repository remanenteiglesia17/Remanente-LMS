@extends('adminlte::page')

@section('title', 'Mis Calificaciones')

@section('content_header')
    <h1 class="m-0 text-dark">
        <i class="fas fa-graduation-cap text-primary"></i> Mis Calificaciones
    </h1>
@stop

@section('content')
    <div class="container-fluid py-3">
        <div class="row">
            @forelse($promedios as $cursoId => $data)
                @php $curso = $data['curso']; @endphp
                <div class="col-lg-6 col-12 mb-4">
                    <div class="card card-outline {{ $data['aprobado'] ? 'card-success' : 'card-danger' }} h-100 shadow-sm">
                        
                        {{-- Encabezado de la Tarjeta --}}
                        <div class="card-header bg-white border-0 pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-light border">Período: {{ $curso->periodo }}</span>
                                <span class="badge {{ $data['aprobado'] ? 'badge-success' : 'badge-danger' }} px-3 py-2">
                                    <i class="fas {{ $data['aprobado'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                                    {{ $data['aprobado'] ? 'Aprobando' : 'En riesgo' }}
                                </span>
                            </div>
                            <h4 class="card-title font-weight-bold mt-2 text-primary" style="font-size: 1.25rem;">
                                {{ $curso->codigo }} - {{ $curso->nombre }}
                            </h4>
                        </div>

                        {{-- Cuerpo de la Tarjeta --}}
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6 border-right">
                                    <span class="text-muted d-block small">Mi promedio ponderado</span>
                                    <h2 class="font-weight-bold my-0 {{ $data['promedio'] >= 3.0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($data['promedio'], 2) }}
                                    </h2>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block small">Promedio del grupo (todos los estudiantes)</span>
                                    <h2 class="font-weight-bold my-0 text-info">
                                        {{ number_format($data['promedio_curso'], 2) }}
                                    </h2>
                                </div>
                            </div>

                            {{-- Barra de Progreso --}}
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progreso de Evaluaciones</span>
                                    <span>{{ $data['total_calificaciones'] }} de {{ $data['total_tareas_curso'] }} ({{ $data['porcentaje_completado'] }}%)</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $data['porcentaje_completado'] }}%" 
                                         aria-valuenow="{{ $data['porcentaje_completado'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pie de la Tarjeta con Acciones --}}
                        <div class="card-footer bg-light border-0 d-flex justify-content-between align-items-center">
                            @if($data['puede_descargar'])
                                <a href="{{ route('certificate.download', $curso->id) }}" 
                                   class="btn btn-sm btn-outline-success" 
                                   title="Descargar Certificado">
                                    <i class="fas fa-certificate"></i> Certificado
                                </a>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled 
                                        title="{{ $data['razon_bloqueo'] }}">
                                    <i class="fas fa-lock"></i> Certificado
                                </button>
                            @endif

                            <a href="{{ route('estudiante.calificaciones.por-curso', $curso->id) }}" 
                               class="btn btn-sm btn-primary px-3">
                                Ver Detalle Completo <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info shadow-sm">
                        <i class="fas fa-info-circle"></i> Aún no tienes calificaciones registradas en tus cursos.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@stop