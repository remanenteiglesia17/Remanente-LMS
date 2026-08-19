@extends('adminlte::page')

@section('title', 'Libro de Calificaciones')

@section('content_header')
    <h1><i class="fas fa-book"></i> Gestión de Calificaciones</h1>
    <a href="{{ route('admin.profesor.parciales.index') }}" class="btn btn-sm btn-outline-primary float-right">
        <i class="fas fa-calendar-check"></i> Parciales y nota final
    </a>
@stop

@section('content')
    <div class="container-fluid">
        {{-- Selector de Curso --}}
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.profesor.calificaciones.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Cursos en su Horario:</label>
                            <select name="curso_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Seleccione un curso --</option>
                                @foreach ($cursos as $curso)
                                    <option value="{{ $curso->id }}"
                                        {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                                        {{ $curso->codigo }} - {{ $curso->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($cursoSeleccionado)
            <div class="card shadow">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Planilla: {{ $cursoSeleccionado->nombre }}</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped m-0" id="tabla-notas">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Estudiante</th>
                                    {{-- CABECERAS DINÁMICAS --}}
                                    @foreach ($tareasDelCurso as $tarea)
                                        <th class="text-center">
                                            @if($tarea->modulo)
                                                <span class="badge badge-secondary d-block mb-1" style="font-size:10px">
                                                    <i class="fas fa-layer-group"></i> {{ $tarea->modulo->nombre }}
                                                </span>
                                            @endif
                                            {{ $tarea->titulo_tarea }} <br>
                                            <small class="badge badge-info">{{ $tarea->porcentaje }}%</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center bg-gray-light">FINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($estudiantes as $index => $estudiante)
                                    <tr>
                                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            <strong>{{ $estudiante->user->name }}</strong>
                                        </td>

                                        {{-- INPUTS DINÁMICOS POR TAREA --}}
                                        @foreach ($tareasDelCurso as $tarea)
                                            <td class="text-center p-1" style="min-width: 130px;">
                                                @php
                                                    // Buscamos la única entrega que vincula a este alumno con esta tarea
                                                    $entrega = $estudiante->entregas->firstWhere(
                                                        'tarea_id',
                                                        $tarea->id,
                                                    );

                                                    $calificacion = $estudiante->calificaciones
                                                        ->where('concepto', $tarea->titulo_tarea)
                                                        ->first();
                                                @endphp

                                                {{-- Input para la nota --}}
                                                <input type="number"
                                                    name="notas[{{ $estudiante->id }}][{{ $tarea->id }}]"
                                                    class="form-control form-control-sm text-center input-nota mb-1"
                                                    step="0.1" min="0" max="5"
                                                    value="{{ $calificacion->nota ?? '' }}"
                                                    placeholder="0.0 – 5.0"
                                                    data-peso="{{ $tarea->porcentaje }}" data-max="5">

                                                @if ($entrega)
                                                    {{-- Botón único de revisión --}}
                                                    <a href="{{ route('admin.profesor.calificaciones.revision', $entrega->id) }}"
                                                        class="btn btn-xs btn-primary btn-block shadow-sm">
                                                        <i class="fas fa-file-alt"></i> Ver Entrega
                                                    </a>
                                                    @if ($entrega->entrega_tardia)
                                                        <small class="text-danger" style="font-size: 0.6rem;">
                                                            <i class="fas fa-clock"></i> Entregada tarde
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary disabled"
                                                        style="font-size: 0.65rem; opacity: 0.6;">
                                                        Sin actividad
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach

                                        @php
                                            // Calcular nota final ponderada desde BD para inicializar la columna
                                            $notaFinalBD = 0;
                                            $pesoBD = 0;
                                            foreach ($tareasDelCurso as $t) {
                                                $cal = $estudiante->calificaciones->where('concepto', $t->titulo_tarea)->first();
                                                if ($cal) {
                                                    $notaFinalBD += ($cal->nota / 5.0) * 5 * ($t->porcentaje / 100);
                                                    $pesoBD += $t->porcentaje;
                                                }
                                            }
                                            if ($pesoBD > 0 && $pesoBD < 100) {
                                                $notaFinalBD = ($notaFinalBD / $pesoBD) * 100;
                                            }
                                            $aprobadoBD = $pesoBD > 0 && $notaFinalBD >= 3.0;
                                        @endphp
                                        <td class="text-center align-middle" style="min-width:90px">
                                            <span class="badge nota-final {{ $pesoBD > 0 ? ($aprobadoBD ? 'badge-success' : 'badge-danger') : 'badge-secondary' }} d-block mb-1"
                                                id="final-{{ $estudiante->id }}"
                                                style="font-size:14px;padding:5px 8px">
                                                {{ $pesoBD > 0 ? number_format($notaFinalBD, 2) : '—' }}
                                            </span>
                                            <span class="estado-final" id="estado-{{ $estudiante->id }}">
                                                @if($pesoBD > 0)
                                                    @if($aprobadoBD)
                                                        <span class="badge badge-success" style="font-size:11px">
                                                            <i class="fas fa-check-circle"></i> Aprobado
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger" style="font-size:11px">
                                                            <i class="fas fa-times-circle"></i> Reprobado
                                                        </span>
                                                    @endif
                                                @endif
                                            </span>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($tareasDelCurso) + 4 }}" class="text-center">No hay
                                            estudiantes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success float-right" onclick="guardarTodo()">
                        <i class="fas fa-save"></i> Guardar Planilla
                    </button>
                </div>
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            calcularFinales();
            $('.input-nota').on('input', calcularFinales);
        });

        function calcularFinales() {
            $('#tabla-notas tbody tr').each(function() {
                let row = $(this);
                let totalPonderado = 0;
                let sumaPesos = 0;

                row.find('.input-nota').each(function() {
                    let nota = parseFloat($(this).val());
                    let peso = parseFloat($(this).data('peso')) || 0;
                    let max  = parseFloat($(this).data('max'))  || 5;

                    if (!isNaN(nota) && max > 0) {
                        // Normalizar a escala 5.0 y ponderar
                        totalPonderado += (nota / max) * 5 * (peso / 100);
                        sumaPesos += peso;
                    }
                });

                let badge = row.find('.nota-final');

                let estadoSpan = row.find('.estado-final');

                if (sumaPesos === 0) {
                    badge.text('—').removeClass('badge-success badge-danger').addClass('badge-secondary');
                    estadoSpan.html('');
                    return;
                }

                // Si los pesos no suman 100%, escalar el resultado
                let notaFinal = sumaPesos < 100
                    ? (totalPonderado / sumaPesos) * 100
                    : totalPonderado;

                badge.text(notaFinal.toFixed(2));

                if (notaFinal >= 3.0) {
                    badge.removeClass('badge-danger badge-secondary').addClass('badge-success');
                    estadoSpan.html('<span class="badge badge-success" style="font-size:11px"><i class="fas fa-check-circle"></i> Aprobado</span>');
                } else {
                    badge.removeClass('badge-success badge-secondary').addClass('badge-danger');
                    estadoSpan.html('<span class="badge badge-danger" style="font-size:11px"><i class="fas fa-times-circle"></i> Reprobado</span>');
                }
            });
        }

        function guardarTodo() {
            // ... lógica de AJAX que ya tienes ...
        }
    </script>
@stop
