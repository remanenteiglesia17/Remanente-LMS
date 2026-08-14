@extends('adminlte::page')

@section('title', 'Mis Calificaciones')

@section('content_header')
    <h1>
        <i class="fas fa-star"></i> Mis Calificaciones
    </h1>
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

        @forelse($promedios as $cursoId => $data)
            @php $curso = $data['curso']; @endphp

            {{-- Información del Curso --}}
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> {{ $curso->codigo }} - {{ $curso->nombre }}
                    </h3>
                    <div class="card-tools d-flex align-items-center gap-1" style="gap:6px">
                        <span class="badge badge-light">Período: {{ $curso->periodo }}</span>
                        <a href="{{ route('estudiante.calificaciones.por-curso', $curso->id) }}" class="badge badge-light ml-1">
                            <i class="fas fa-eye"></i> Ver detalle
                        </a>

                        {{-- ── Botón Certificado ── --}}
                        @php
                            $cursoPivot = auth()->user()->estudiante
                                ->cursos()->where('cursos.id', $curso->id)
                                ->withPivot('horas_realizadas', 'estado')->first();
                            $estadoPivot  = $cursoPivot?->pivot->estado ?? 'activo';
                            $notaOk       = $data['aprobado'];
                            $horasOk      = $cursoPivot && $cursoPivot->pivot->horas_realizadas >= $curso->horas_requeridas;
                            $puedeDescargar = ($estadoPivot === 'aprobado') || ($notaOk && $horasOk);
                        @endphp

                        @if($puedeDescargar)
                            <a href="{{ route('certificate.download', $curso->id) }}"
                               class="btn btn-sm btn-success ml-1"
                               title="¡Felicitaciones! Descarga tu certificado">
                                <i class="fas fa-certificate"></i> Obtener Certificado
                            </a>
                        @else
                            @php
                                $razon = !$notaOk ? 'Nota insuficiente' : 'Pendiente de aprobación';
                            @endphp
                            <button class="btn btn-sm btn-secondary ml-1" disabled
                                    title="El profesor debe marcar el curso como Aprobado en Inscripciones, o debes aprobar con nota ≥ 3.0">
                                <i class="fas fa-lock"></i> Certificado
                                <span class="badge badge-light ml-1" style="font-size:9px">
                                    {{ $razon }}
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="info-box {{ $data['promedio'] >= 3.0 ? 'bg-success' : 'bg-danger' }}">
                                <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Mi Nota Final</span>
                                    <span class="info-box-number">{{ number_format($data['promedio'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Promedio del Curso</span>
                                    <span class="info-box-number">{{ number_format($data['promedio_curso'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Evaluaciones Calificadas</span>
                                    <span class="info-box-number">{{ $data['total_calificaciones'] }}/{{ $data['total_tareas_curso'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Porcentaje Completado</span>
                                    <span class="info-box-number">{{ $data['porcentaje_completado'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($data['promedio'] > 0)
                        <span class="badge {{ $data['aprobado'] ? 'badge-success' : 'badge-danger' }} p-2">
                            <i class="fas {{ $data['aprobado'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $data['aprobado'] ? 'Vas aprobando el curso' : 'Vas reprobando el curso' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Desglose de Calificaciones por tipo de evaluación --}}
            <div class="row">
                @forelse($data['por_tipo'] as $tipo => $grupo)
                    @php $meta = $etiquetasTipo[$tipo] ?? ['label' => ucfirst($tipo), 'icon' => 'fa-star', 'color' => 'secondary']; @endphp
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-{{ $meta['color'] }}">
                                <h3 class="card-title">
                                    <i class="fas {{ $meta['icon'] }}"></i> {{ $meta['label'] }} ({{ $grupo['peso_total'] }}%)
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ $meta['label'] }}</th>
                                            <th width="100" class="text-center">Nota</th>
                                            <th width="120" class="text-center">Peso</th>
                                            <th width="110" class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grupo['items'] as $calif)
                                            <tr>
                                                <td>{{ $calif->concepto }}</td>
                                                <td class="text-center">
                                                    <strong class="text-{{ $calif->color }}">
                                                        {{ number_format($calif->nota, 2) }}
                                                    </strong>
                                                </td>
                                                <td class="text-center">{{ $calif->porcentaje }}%</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $calif->nota >= 3.0 ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $calif->nota >= 3.0 ? 'Calificada' : 'Reprobada' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th>Promedio {{ $meta['label'] }}</th>
                                            <th class="text-center">{{ number_format($grupo['promedio'], 2) }}</th>
                                            <th class="text-center">{{ $grupo['peso_total'] }}%</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>

        @empty
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Aún no tienes calificaciones publicadas. Cuando tu profesor califique y publique
                tus entregas, las verás reflejadas aquí automáticamente.
            </div>
        @endforelse
    </div>
@stop
