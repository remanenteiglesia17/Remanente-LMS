@extends('adminlte::page')

@section('title', 'Asistencia')

@section('content_header')
    <h1>Control de Asistencias</h1>
@endsection

@section('content')
<div class="container-fluid py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Navegación de Pestañas (Tabs de Bootstrap 4 / AdminLTE 3) -->
    <ul class="nav nav-tabs mb-4" id="asistenciaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="tomar-tab" data-toggle="tab" href="#tomar-panel" role="tab" aria-controls="tomar-panel" aria-selected="true">
                <i class="fas fa-user-check mr-2"></i>Tomar Asistencia
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="inasistencias-tab" data-toggle="tab" href="#inasistencias-panel" role="tab" aria-controls="inasistencias-panel" aria-selected="false">
                <i class="fas fa-user-times mr-2"></i>Reporte de Inasistencias 
                <span class="badge badge-danger ml-1">{{ $inasistencias->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content" id="asistenciaTabsContent">
        
        <!-- PESTAÑA 1: TOMAR ASISTENCIA -->
        <div class="tab-pane fade show active" id="tomar-panel" role="tabpanel" aria-labelledby="tomar-tab">
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Próximas Clases Registradas</h3>
                </div>
                <div class="card-body">
                    @if($clases->isEmpty())
                        <div class="alert alert-info mb-0">No hay clases programadas para gestionar en este momento.</div>
                    @else
                        <div class="accordion" id="accordionClases">
                            @foreach($clases as $clase)
                                <div class="card mb-2">
                                    <div class="card-header p-2" id="heading{{ $clase->id }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapse{{ $clase->id }}" aria-expanded="false" aria-controls="collapse{{ $clase->id }}">
                                                <i class="fas fa-chevron-right mr-2"></i> {{ $clase->titulo }} 
                                                <span class="text-muted font-weight-normal">| {{ $clase->curso->nombre ?? 'Sin curso' }}</span>
                                                <small class="float-right text-muted">{{ \Carbon\Carbon::parse($clase->fecha_hora_inicio)->format('d/m/Y H:i') }}</small>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse{{ $clase->id }}" class="collapse" aria-labelledby="heading{{ $clase->id }}" data-parent="#accordionClases">
                                        <div class="card-body">
                                            <form class="form-asistencia" data-clase-id="{{ $clase->id }}">
                                                @csrf
                                                <input type="hidden" name="clase_id" value="{{ $clase->id }}">
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped align-middle">
                                                        <thead>
                                                            <tr>
                                                                <th>Estudiante</th>
                                                                <th class="text-center">Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($clase->estudiantes as $index => $estudiante)
                                                                @php
                                                                    $key = $clase->id . '-' . $estudiante->id;
                                                                    $estadoActual = $asistencias->has($key) ? $asistencias[$key]->estado : 'presente';
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        {{ $estudiante->user->name ?? $estudiante->nombre_completo }}
                                                                        <input type="hidden" name="asistencias[{{ $index }}][estudiante_id]" value="{{ $estudiante->id }}">
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                            <label class="btn btn-outline-success btn-sm {{ $estadoActual === 'presente' ? 'active' : '' }}">
                                                                                <input type="radio" name="asistencias[{{ $index }}][estado]" value="presente" {{ $estadoActual === 'presente' ? 'checked' : '' }}> Presente
                                                                            </label>
                                                                            <label class="btn btn-outline-danger btn-sm {{ $estadoActual === 'ausente' ? 'active' : '' }}">
                                                                                <input type="radio" name="asistencias[{{ $index }}][estado]" value="ausente" {{ $estadoActual === 'ausente' ? 'checked' : '' }}> Ausente
                                                                            </label>
                                                                            <label class="btn btn-outline-warning btn-sm {{ $estadoActual === 'tardanza' ? 'active' : '' }}">
                                                                                <input type="radio" name="asistencias[{{ $index }}][estado]" value="tardanza" {{ $estadoActual === 'tardanza' ? 'checked' : '' }}> Tardanza
                                                                            </label>
                                                                            <label class="btn btn-outline-info btn-sm {{ $estadoActual === 'excusado' ? 'active' : '' }}">
                                                                                <input type="radio" name="asistencias[{{ $index }}][estado]" value="excusado" {{ $estadoActual === 'excusado' ? 'checked' : '' }}> Excusado
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="d-flex justify-content-end mt-3">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save mr-1"></i> Guardar Asistencia
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- PESTAÑA 2: REPORTE DE INASISTENCIAS -->
        <div class="tab-pane fade" id="inasistencias-panel" role="tabpanel" aria-labelledby="inasistencias-tab">
            <div class="card card-outline card-danger shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold text-danger">Historial de Faltas Registradas</h3>
                </div>
                <div class="card-body">
                    @if($inasistencias->isEmpty())
                        <div class="alert alert-success mb-0">No hay inasistencias registradas pendientes de excusa.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Curso</th>
                                        <th>Clase</th>
                                        <th>Fecha / Hora</th>
                                        <th>Duración (Horas)</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inasistencias as $item)
                                        <tr>
                                            <td><strong>{{ $item->user->name ?? 'N/A' }}</strong></td>
                                            <td>{{ $item->nombre_curso }}</td>
                                            <td>{{ $item->nombre_clase }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->fecha_hora_inicio)->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge badge-secondary">{{ $item->cant_horas }} hrs</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        data-toggle="modal" 
                                                        data-target="#modalExcusar" 
                                                        data-asistencia-id="{{ $item->asistencia_id }}"
                                                        data-estudiante="{{ $item->user->name ?? '' }}">
                                                    <i class="fas fa-notes-medical"></i> Excusar
                                                </button>
                                                <!-- <a href="{{ route('admin.asistencias.estadisticas', $item->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-chart-pie"></i> Stats
                                                </a> -->
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal para Excusar Inasistencia (Bootstrap 4) -->
<div class="modal fade" id="modalExcusar" tabindex="-1" role="dialog" aria-labelledby="modalExcusarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formExcusar" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcusarLabel">Excusar Inasistencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Estudiante: <strong id="modalEstudianteNombre"></strong></p>
                    <div class="form-group">
                        <label for="observaciones">Observación / Justificación</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" required placeholder="Motivo de la excusa (Incapacidad médica, calamidad doméstica, etc.)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Excusa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function () {
    // Manejo de envío AJAX para la toma de asistencias
    $('.form-asistencia').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch("{{ route('admin.asistencias.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Cargar datos en el Modal de Excusar con jQuery
    $('#modalExcusar').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const asistenciaId = button.data('asistencia-id');
        const estudianteNombre = button.data('estudiante');

        $('#modalEstudianteNombre').text(estudianteNombre);
        
        const actionUrl = "{{ url('admin/asistencias/excusar') }}/" + asistenciaId;
        $('#formExcusar').attr('action', actionUrl);
    });
});
</script>
@endpush