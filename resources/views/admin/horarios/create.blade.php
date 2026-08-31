@extends('adminlte::page')

@section('title', ucfirst(auth()->user()->getRoleNames()->first()))
@section('css')
<style>
    .curso-ellipsis {
        max-width: 120px;
        /* ajusta según ancho de la columna */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
    }
</style>
@stop
@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">Registro de un nuevo horario</h1>

    <a href="{{ route('admin.home') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
</div>
@stop


@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Llene los Datos</h3>
        </div>
        <div class="card-body">
            {{-- ALERTAS SEGÚN PERMISOS --}}
            @can('admin.horarios.crear_nuevos')
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Permisos completos:</strong>
                Puede crear nuevos horarios libremente y modificar existentes.
            </div>
            @else
            @can('admin.horarios.agendar_dia_libre')
            @can('admin.horarios.modificar_existentes')
            <div class="alert alert-info">
                <i class="fas fa-calendar-check"></i> <strong>Permisos mixtos:</strong>
                Puede modificar horarios existentes y agendar en días sin horarios previos.
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-calendar-plus"></i> <strong>Solo días libres:</strong>
                Solo puede agendar en días donde el profesor NO tiene horarios configurados.
            </div>
            @endcan
            @else
            @can('admin.horarios.modificar_existentes')
            <div class="alert alert-warning">
                <i class="fas fa-edit"></i> <strong>Solo modificar:</strong>
                Solo puede editar horarios ya existentes. Seleccione un profesor para ver sus horarios.
            </div>
            @else
            <div class="alert alert-danger">
                <i class="fas fa-ban"></i> <strong>Sin permisos:</strong>
                No tiene permisos para gestionar horarios.
            </div>
            @endcan
            @endcan
            @endcan

            <div class="row">
                <div class="col-md-2">
                    <form action="{{ route('admin.horarios.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label for="profesor_id">Profesores </label><b class="text-danger">*</b>
                            <select class="form-control" name="profesor_id" id="profesor_id" required>
                                <option value="" selected disabled>Seleccione</option>
                                @foreach ($profesores as $profesor)
                                <option value="{{ $profesor->id }}">
                                    {{ $profesor->nombres . ' ' . $profesor->apellidos }}
                                </option>
                                @endforeach
                            </select>
                            @error('profesor_id')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- SELECTOR DE HORARIO EXISTENTE (solo para modificar_existentes) --}}
                        @can('admin.horarios.modificar_existentes')
                        @cannot('admin.horarios.crear_nuevos')
                        <div class="form-group" id="horario_existente_group" style="display: none;">
                            <label for="horario_existente">Horario a modificar</label><b class="text-danger">*</b>
                            <select class="form-control" id="horario_existente">
                                <option value="" selected>Primero seleccione un profesor</option>
                            </select>
                            <small class="text-muted">Seleccione el horario que desea modificar</small>
                        </div>
                        @endcannot
                        @endcan

                        {{-- SELECCIÓN DE CURSOS --}}
                        {{-- UN SOLO CURSO --}}
                        <div class="form-group">
                            <label for="curso_id">Curso</label><b class="text-danger">*</b>
                            <select name="cursos[]" id="curso_select" class="form-control" required>
                                <option value="" disabled selected>Seleccione</option>
                                @foreach ($cursos as $curso)
                                <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                                @endforeach
                            </select>
                            @error('cursos')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dia">Día </label><b class="text-danger">*</b>
                            <select class="form-control" name="dia" id="dia" required>
                                <option value="" selected disabled>Seleccione</option>
                                <option value="LUNES">LUNES</option>
                                <option value="MARTES">MARTES</option>
                                <option value="MIERCOLES">MIÉRCOLES</option>
                                <option value="JUEVES">JUEVES</option>
                                <option value="VIERNES">VIERNES</option>
                                <option value="SABADO">SÁBADO</option>
                                <option value="DOMINGO">DOMINGO</option>
                            </select>
                            @error('dia')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">Hora Inicio </label><b class="text-danger">*</b>
                            <input type="time" class="form-control" name="hora_inicio" id="hora_inicio" required>
                            @error('hora_inicio')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="hora_fin">Hora Final </label><b class="text-danger">*</b>
                            <input type="time" class="form-control" name="hora_fin" id="hora_fin" required>
                            @error('hora_fin')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio </label><b class="text-danger">*</b>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" required>
                            @error('fecha_inicio')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin </label><b class="text-danger">*</b>
                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" required>
                            @error('fecha_fin')
                            <small class="bg-danger text-white p-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                @can('admin.horarios.crear_nuevos')
                                <i class="fas fa-save"></i>
                                @else
                                @can('admin.horarios.modificar_existentes')
                                Actualizar horario
                                @else
                                Agendar
                                @endcan
                                @endcan
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-10">
                    <hr>
                    <div id="curso_info"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
{{-- Datos de horarios existentes para JavaScript --}}
@can('admin.horarios.modificar_existentes')
@cannot('admin.horarios.crear_nuevos')
<script>
    const horariosExistentes = @json($horariosExistentes ?? []);
    const puedeModificarExistentes = true;
    console.log('Horarios existentes:', horariosExistentes);
</script>
@endcannot
@endcan

<script>
$('#profesor_id').on('change', function() {
    var profesor_id = $(this).val();
    if (!profesor_id) return;

    var url = "{{ route('admin.horarios.show_datos_cursos', ':id') }}";
    url = url.replace(':id', profesor_id);

    $.get(url, function(response) {
        // 1. Actualizar la tabla de información inferior
        $('#curso_info').html(response.html_tabla);

        // 2. Referencia al select de cursos
        var select = $('#curso_select');
        select.empty();

        // Un profesor puede dictar más de un curso: siempre se muestra la
        // lista completa y editable. Si ya tiene un curso con horario
        // asignado, solo se le informa (no se bloquea la selección).
        select.append('<option value="" disabled selected>Seleccione un curso</option>');
        select.css({
            'pointer-events': 'auto',
            'background-color': '#ffffff',
            'cursor': 'default'
        });

        // response.cursos.forEach(function(curso) {
        //     var esElYaAsignado = response.tiene_curso && response.curso_asignado && curso.id === response.curso_asignado.id;
        //     select.append(`<option value="${curso.id}"${esElYaAsignado ? ' selected' : ''}>${curso.nombre}${esElYaAsignado ? ' (ya tiene horario)' : ''}</option>`);
        // });

        if (response.mensaje) {
            console.log(response.mensaje);
        }

        // Gracias a que no refrescamos la página, el cursor no saltará el campo IVA
        // si el usuario sigue su flujo normal de tabulación o clic.
    }).fail(function(xhr) {
        // Muestra el mensaje real que manda el backend (si lo hay) para
        // poder diagnosticar la causa real en vez de un mensaje genérico.
        var detalle = xhr.responseJSON && xhr.responseJSON.error
            ? xhr.responseJSON.error
            : ('HTTP ' + xhr.status + ' — revisa storage/logs/laravel.log');
        console.error('Error al obtener los datos del profesor:', xhr);
        alert('Error al obtener los datos del profesor:\n' + detalle);
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const horaInicio = document.getElementById('hora_inicio');
        const horaFin = document.getElementById('hora_fin');

        horaInicio.addEventListener('change', function() {
            let selectedTime = this.value;

            if (selectedTime) {
                selectedTime = selectedTime.split(':');
                selectedTime = selectedTime[0] + ':00';
                this.value = selectedTime;
            }

            if (selectedTime < '06:00' || selectedTime > '20:00') {
                this.value = null;
                Swal.fire({
                    title: "No fue posible",
                    text: "Por favor seleccione una hora entre 06:00 am y 8:00 pm",
                    icon: "info"
                });
            }
        });

        horaFin.addEventListener('change', function() {
            let selectedTime = this.value;

            selectedTime = selectedTime.split(':')[0] + ':00';
            this.value = selectedTime;

            if (selectedTime < '06:00' || selectedTime > '20:00') {
                this.value = null;
                Swal.fire({
                    title: "No fue posible",
                    text: "Por favor seleccione una hora entre 06:00 am y 8:00 pm",
                    icon: "info"
                });
            }
        });

        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');

        fechaInicio.addEventListener('change', function() {
            fechaFin.min = this.value;
            if (fechaFin.value && fechaFin.value < this.value) {
                fechaFin.value = '';
                Swal.fire({
                    title: "No fue posible",
                    text: "La fecha fin no puede ser anterior a la fecha inicio.",
                    icon: "info"
                });
            }
        });
    });
</script>
@stop