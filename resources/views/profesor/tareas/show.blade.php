@extends('adminlte::page')

@section('title', 'Detalle de tarea')

@section('content')
<div class="container-fluid">

    <div class="card card-outline card-primary mt-3">
        <div class="card-header">
            <h3 class="card-title"><strong>{{ $tarea->titulo_tarea }}</strong></h3>
            <div class="card-tools">
                <a href="{{ route('admin.profesor.tareas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Curso:</strong> {{ $tarea->curso->nombre ?? 'N/A' }}</p>
            <p>
                <strong>Módulo:</strong> 
                @if($tarea->modulo)
                    <span class="badge badge-info">{{ $tarea->modulo->nombre }}</span>
                @else
                    <span class="badge badge-secondary">Sin módulo asignado</span>
                @endif
            </p>
            <p><strong>Descripción:</strong> {!! nl2br(e($tarea->descripcion_tarea)) !!}</p>

            <hr>

            <p>
                <strong>Fecha límite:</strong> {{ $tarea->fecha_entrega ? \Carbon\Carbon::parse($tarea->fecha_entrega)->format('d/m/Y H:i') : 'No definida' }} <br>
                <strong>Puntaje máximo:</strong> {{ $tarea->puntaje }} pts
            </p>

            {{-- DOCUMENTOS DEL PROFESOR --}}
            <h5><i class="fas fa-paperclip"></i> Documentos Adjuntos</h5>
            @if($tarea->documentos->count() > 0)
                <ul>
                    @foreach($tarea->documentos as $doc)
                        <li>
                            <a href="{{ asset('storage/'.$doc->archivo) }}" target="_blank">
                                {{ $doc->titulo }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No hay documentos adjuntos.</p>
            @endif
        </div>
    </div>

    {{-- ENTREGAS DE ESTUDIANTES --}}
    <div class="card card-outline card-success">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-user-check"></i> Entregas de estudiantes</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Fecha entrega</th>
                        <th>Calificación</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tarea->entregas as $entrega)
                    <tr>
                        <td>{{ $entrega->user->name ?? $entrega->estudiante->name ?? 'Estudiante' }}</td>
                        <td>{{ $entrega->created_at ? $entrega->created_at->format('d/m/Y H:i') : $entrega->fecha_entrega }}</td>
                        <td>
                            @if(is_null($entrega->calificacion))
                                <span class="badge badge-warning">Sin calificar</span>
                            @else
                                <span class="badge badge-success">{{ $entrega->calificacion }} / {{ $tarea->puntaje }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary"
                                data-toggle="modal"
                                data-target="#calificar{{ $entrega->id }}">
                                <i class="fas fa-edit"></i> Calificar
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL CALIFICAR --}}
                    <div class="modal fade" id="calificar{{ $entrega->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <form method="POST" action="{{ route('admin.profesor.entregas.calificar', $entrega) }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Calificar Entrega</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Calificación (Máx. {{ $tarea->puntaje }})</label>
                                            <input type="number"
                                                name="calificacion"
                                                class="form-control"
                                                max="{{ $tarea->puntaje }}"
                                                min="0"
                                                step="0.01"
                                                value="{{ $entrega->calificacion }}"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label>Comentario</label>
                                            <textarea name="comentario_profesor"
                                                class="form-control"
                                                rows="3">{{ $entrega->comentario_profesor }}</textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No hay entregas registradas aún.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection